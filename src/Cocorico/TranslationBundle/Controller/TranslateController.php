<?php

namespace Cocorico\TranslationBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class Translate
 * @package Cocorico\TranslationBundle\Controller
 */
class TranslateController extends \JMS\TranslationBundle\Controller\TranslateController
{
    /**
     * @Route("/", name="jms_translation_index", options = {"i18n" = false})
     * @Template("@Cocorico/Translate/index.html.twig")
     *
     * @param Request $request
     * @return array
     */
    public function indexAction(Request $request)
    {
        return parent::indexAction($request);
    }

}
