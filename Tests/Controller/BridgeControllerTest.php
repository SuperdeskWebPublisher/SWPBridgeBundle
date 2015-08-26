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

namespace SWP\BridgeBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;

class BridgeControllerTest extends WebTestCase
{
    protected function setUp()
    {
        $kernel = new \AppKernel('test', true);
        $kernel->boot();

        $this->client = $kernel->getContainer()->get('test.client');
    }

    /**
     * @expectedException \Superdesk\ContentApiSdk\Exception\ContentApiException
     */
    public function testIndexForCallToInvalidEndpoints()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/bridge/invalid_endpoint/');
    }
}
