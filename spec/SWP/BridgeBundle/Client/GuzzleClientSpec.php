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

namespace spec\SWP\BridgeBundle\Client;

use PhpSpec\ObjectBehavior;

class GuzzleClientSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('SWP\BridgeBundle\Client\GuzzleClient');
        $this->shouldImplement('Superdesk\ContentApiSdk\Client\ClientInterface');
    }

    function let()
    {
        $config = array('base_uri' => 'http://httpbin.org');
        $this->beConstructedWith($config);
    }

    function it_should_make_a_call_to_a_remote_server()
    {
        $this->makeApiCall('/status/200', null, null)->shouldBe('');
    }

    function it_should_throw_an_exception_when_an_error_occurs()
    {
        $this->shouldThrow('\Superdesk\ContentApiSdk\Exception\ContentApiException')->duringMakeApiCall('/status/404', null, null);
        $this->shouldThrow('\Superdesk\ContentApiSdk\Exception\ContentApiException')->duringMakeApiCall('/status/500', null, null);
    }

    function it_should_throw_an_exception_on_invalid_baseuri()
    {
        $config = array('base_uri' => '');
        $this->beConstructedWith($config);
        $this->shouldThrow('\Superdesk\ContentApiSdk\Exception\ContentApiException')->duringMakeApiCall('', null, null);
    }

    function it_should_be_able_to_return_a_response_as_a_string()
    {
        $this->makeApiCall('/headers', null, null, false)->shouldBeString();
    }

    function it_should_be_able_to_return_a_response_as_valid_array_format()
    {
        $response = $this->makeApiCall('/headers', null, null, true);
        $response->shouldBeArray();
        $response->shouldHaveKey('headers');
        $response->shouldHaveKey('status');
        $response->shouldHaveKey('reason');
        $response->shouldHaveKey('version');
        $response->shouldHaveKey('body');

        $response['headers']->shouldBeArray();
        $response['status']->shouldBe(200);
        $response['reason']->shouldBeString();
        $response['version']->shouldEqual("1.1");
        $response['body']->shouldBeString();
    }

    function it_should_be_able_to_return_json()
    {
        $config = array(
            'base_uri' => 'http://httpbin.org',
            'options' => array(
                'Content-Type' => 'application/json'
            )
        );
        $this->beConstructedWith($config);
        $response = $this->makeApiCall('/headers', null, null, true);
        $response['headers']->shouldHaveKey('Content-Type');
        $response['headers']['Content-Type']->shouldContain('application/json');
    }

    function it_should_be_able_to_return_xml()
    {
        $config = array(
            'base_uri' => 'http://httpbin.org',
            'options' => array(
                'Content-Type' => 'application/xml'
            )
        );
        $this->beConstructedWith($config);
        $response = $this->makeApiCall('/xml', null, null, true);
        $response['headers']->shouldHaveKey('Content-Type');
        $response['headers']['Content-Type']->shouldContain('application/xml');
    }
}
