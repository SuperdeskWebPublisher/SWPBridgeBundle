<?php

/**
 * This file is part of the Superdesk Web Publisher Bridge for the Content API.
 *
 * Copyright 2015 Sourcefabric z.u. and contributors.
 *
 * For the full copyright and license information, please see the
 * AUTHORS and LICENSE files distributed with this source code.
 *
 * @copyright 2015 Sourcefabric z.ú.
 * @license http://www.superdesk.org/license
 */

namespace SWP\BridgeBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class SWPBridgeExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $defaultOptions = array();
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        if (isset($config['api'])) {
            if (!empty($config['api']['host'])) {
                $container->setParameter($this->getAlias().'.api.host', $config['api']['host']);
            }
            if (!empty($config['api']['port'])) {
                $container->setParameter($this->getAlias().'.api.port', $config['api']['port']);
            }
            if (!empty($config['api']['protocol'])) {
                $container->setParameter($this->getAlias().'.api.protocol', $config['api']['protocol']);
            }
        }

        if (isset($config['auth'])) {
            if (!empty($config['auth']['client_id'])) {
                $container->setParameter($this->getAlias().'.auth.client_id', $config['auth']['client_id']);
            }
            if (!empty($config['auth']['username'])) {
                $container->setParameter($this->getAlias().'.auth.username', $config['auth']['username']);
            }
            if (!empty($config['auth']['password'])) {
                $container->setParameter($this->getAlias().'.auth.password', $config['auth']['password']);
            }
        }

        if (isset($config['options']) && is_array($config['options'])) {
            $defaultOptions = $config['options'];
        }
        $container->setParameter($this->getAlias().'.options', $defaultOptions);
    }
}
