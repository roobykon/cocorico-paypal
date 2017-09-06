<?php

namespace Cocorico\TranslationBundle\Controller;

use JMS\DiExtraBundle\Annotation as DI;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Cocorico\TranslationBundle\Translation\Creator;
use JMS\TranslationBundle\Exception\RuntimeException;
use JMS\TranslationBundle\Translation\ConfigFactory;
use Symfony\Component\HttpFoundation\Response;
use JMS\TranslationBundle\Util\FileUtils;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class ApiController
 * @package Cocorico\TranslationBundle\Controller
 */
class ApiController extends \JMS\TranslationBundle\Controller\ApiController
{
    /**
     * @var ConfigFactory
     *
     * @DI\Inject("jms_translation.config_factory")
     */
    private $config;

    /**
     * @var Creator
     *
     * @DI\Inject("jms_translation.creator")
     */
    protected $creator;

    /**
     * @Route("/configs/{config}/domains/{domain}/messages",
     *            name="jms_translation_create_message",
     *            defaults = {"id" = null},
     *            options = {"i18n" = false})
     * @Method("POST")
     *
     * @param Request $request
     * @param $config
     * @param $domain
     * @return Response
     */
    public function createMessageAction(Request $request, $config, $domain)
    {
        $id = $request->query->get('id');
        $locales = $request->request->get('locales');

        foreach ($locales as $locale) {
            $configuration = $this->config->getConfig($config, $locale);

            $files = FileUtils::findTranslationFiles($configuration->getTranslationsDir());
            if (!isset($files[$domain][$locale])) {
                throw new RuntimeException(sprintf('There is no translation file for domain "%s" and locale %s.', $domain, $locale));
            }

            list($format, $file) = $files[$domain][$locale];
            $this->creator->createTranslation($file, $format, $domain, $locale, $id);
        }

        return new Response();
    }

}
