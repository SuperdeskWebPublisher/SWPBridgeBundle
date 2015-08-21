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

namespace SWP\BridgeBundle\Tests\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use SWP\BridgeBundle\DependencyInjection\SWPBridgeExtension;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

class SWPBridgeExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers SWP\BridgeBundle\SWPBridgeBundle
     * @covers SWP\BridgeBundle\DependencyInjection\SWPBridgeExtension::load
     * @covers SWP\BridgeBundle\DependencyInjection\Configuration::getConfigTreeBuilder
     */
    public function testLoad()
    {
        $curlOptions = array(
            'curl' => array(
                '10203' => 'example.com:5050:localhost',
            ),
        );

        $data = array(
            'swp_bridge.base_uri' => 'http://example.com',
            'swp_bridge.options' => $curlOptions,
        );

        $container = $this->createContainer($data);
        $loader = $this->createLoader();
        $config = $this->getConfig();

        $loader->load(array($config), $container);

        $this->assertEquals('http://example.com', $container->getParameter('swp_bridge.base_uri'));
        $this->assertEquals($curlOptions, $container->getParameter('swp_bridge.options'));
    }

    /**
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testLoadWhenBaseUriIsRequiredAndCannotBeEmpty()
    {
        $container = $this->createContainer();
        $loader = $this->createLoader();

        $config = array(
            'swp_bridge.base_uri' => '',
        );

        $loader->load(array($config), $container);
    }

    protected function createLoader()
    {
        return new SWPBridgeExtension();
    }

    protected function getConfig()
    {
        return array(
            'base_uri' => 'http://example.com',
            'options' => array(
                'curl' => array(
                    '10203' => 'example.com:5050:localhost',
                ),
            )
        );
    }

    protected function createContainer(array $data = array())
    {
        return new ContainerBuilder(new ParameterBag($data));
    }
}
