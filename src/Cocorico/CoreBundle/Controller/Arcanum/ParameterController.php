<?php

namespace Cocorico\CoreBundle\Controller\Arcanum;

use JMS\DiExtraBundle\Annotation as DI;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;
use AppKernel;
use Exception;

/**
 * Class ParameterController
 * @package Cocorico\CoreBundle\Controller\Arcanum
 */
class ParameterController extends Controller
{
    const FILEPATH_RELATIVE = '/config/parameters.yml';

    /**
     * @Route("/arcanum/parameters")
     *
     * @Method({"GET"})
     *
     * @param  Request $request
     *
     * @return Response
     */
    public function indexAction(Request $request)
    {
        $parameters = $this->getParameters();

        return $this->render('CocoricoCoreBundle:Arcanum/Parameter:index.html.twig', [
            'parameters' => $parameters,
            'site_name' => $parameters['cocorico.site_name'],
            'environment' => $this->getKernel()->getEnvironment(),
        ]);
    }

    /**
     * @Route("/arcanum/parameter/save", name="arcanum_parameters_save")
     *
     * @Method({"POST"})
     *
     * @param  Request $request
     *
     * @return JsonResponse
     */
    public function saveAction(Request $request)
    {
        $parameters = [
            'all' => $this->getParameters(),
            'save' => $request->request->all(),
        ];

        $changed = false;
        foreach ($parameters['all'] as $parameter => $value) {
            $alias = str_replace('.', '_', $parameter);
            if (empty($parameters['save'][$alias])) {
                continue;
            }
            if ($parameters['save'][$alias] == $value) {
                continue;
            }

            $changed || $changed = true;
            $parameters['all'][$parameter] = $parameters['save'][$alias];
        }

        $response = [];
        if (!$changed) {
            $response['success'] = (int)$changed;
            $response['message'] = 'params do not changed';
        } elseif (!$this->storeParameters(['parameters' => $parameters['all']])) {
            $response['success'] = 0;
            $response['message'] = 'params do not stored';
        } else {
            $response['success'] = 1;
            $response['message'] = 'params stored successfully';
        }

        return new JsonResponse($response);
    }

    /**
     * @Route("/arcanum/parameter/reload", name="arcanum_parameters_reload")
     *
     * @Method({"POST"})
     *
     * @param  Request $request
     *
     * @return JsonResponse
     */
    public function reloadAction(Request $request)
    {
        return new JsonResponse(['success' => $this->clearCache()]);
    }

    /**
     * Clear cache
     *
     * @return bool
     */
    protected function clearCache()
    {
        $fs = new Filesystem();
        $fs->remove($this->container->getParameter('kernel.cache_dir'));

        return true;

        $kernel = $this->getKernel();
        $command = sprintf('php %s/console cache:clear --env=%s',
            $kernel->getRootDir(),
            $kernel->getEnvironment()
        );
        $process = new Process($command);

        try {
            $process->mustRun();
            $content = $process->getOutput();
            return true;
        } catch (ProcessFailedException $e) {
            $content = $e->getMessage();
            return false;
        } catch (Exception $e) {
            $content = $e->getMessage();
            return false;
        }
    }

    /**
     * @param string $filepath
     * @return string
     */
    protected function getAbsoluteFilePath($filepath)
    {
        return $this->getKernel()->getRootDir() . $filepath;
    }

    /**
     * @param string $filepath
     * @return array
     */
    protected function getParameters($filepath = self::FILEPATH_RELATIVE)
    {
        $filepath = $this->getAbsoluteFilePath($filepath);
        try {
            return Yaml::parse(file_get_contents($filepath))['parameters'];
        } catch (ParseException $e) {
            printf("Unable to parse the YAML string: %s", $e->getMessage());
            return [];
        }
    }

    /**
     * @param array $parameters
     * @param string $filepath
     * @return bool
     */
    protected function storeParameters(array $parameters, $filepath = self::FILEPATH_RELATIVE)
    {
        $filepath = $this->getAbsoluteFilePath($filepath);

        return (bool)file_put_contents($filepath, Yaml::dump($parameters));
    }

    /**
     * @return AppKernel
     */
    protected function getKernel()
    {
        return $this->get('kernel');
    }

}
