<?php

namespace Cocorico\TranslationBundle\DependencyInjection;

use JMS\TranslationBundle\DependencyInjection\JMSTranslationExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader;

/**
 * Class CocoricoTranslationExtension
 * @package Cocorico\TranslationBundle\DependencyInjection
 */
class CocoricoTranslationExtension extends JMSTranslationExtension
{
    /**
     * @param array $configs
     * @param ContainerBuilder $container
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');

        parent::load($configs, $container);
    }

}
