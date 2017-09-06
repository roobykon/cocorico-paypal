<?php

namespace Cocorico\PaymentBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $builder = new TreeBuilder();
        $builder
            ->root('cocorico_payment', 'array')
            ->children()
            ->arrayNode('paypal_ec')
            ->prototype('scalar')
            ->end()
            ->end()
            ->arrayNode('paypal_ap')
            ->prototype('scalar')
            ->end();

        return $builder;
    }

}
