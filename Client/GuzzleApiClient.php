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

namespace SWP\BridgeBundle\Client;

use Superdesk\ContentApiSdk\API\Request\RequestInterface;
use Superdesk\ContentApiSdk\API\Request\OAuthDecorator;
use Superdesk\ContentApiSdk\API\Response;
use Superdesk\ContentApiSdk\ContentApiSdk;
use Superdesk\ContentApiSdk\Client\AbstractApiClient;
use Superdesk\ContentApiSdk\Client\ClientInterface;
use Superdesk\ContentApiSdk\Exception\AccessDeniedException;
use Superdesk\ContentApiSdk\Exception\AuthenticationException;
use Superdesk\ContentApiSdk\Exception\ClientException;
use Superdesk\ContentApiSdk\Exception\ContentApiException;
use Superdesk\ContentApiSdk\Exception\ResponseException;

/**
 * Request service that implements all method regarding basic request/response
 * handling.
 */
class GuzzleApiClient extends AbstractApiClient
{
    /**
     * Default request headers.
     *
     * @var array
     */
    protected $headers = array(
        'Accept' => 'application/json'
    );

    /**
     * Options which come from Bundle configuration.
     *
     * @var array
     */
    protected $options = array();

    /**
     * {@inheritdoc}
     */
    public function makeApiCall(RequestInterface $request)
    {
        $response = null;

        if ($this->authenticator->getAccessToken() !== null) {
            $authenticatedRequest = new OAuthDecorator($request);
            $authenticatedRequest->setAccessToken($this->authenticator->getAccessToken());
            $authenticatedRequest->addAuthentication();

            $response = $this->client->makeCall(
                $authenticatedRequest->getFullUrl(),
                $this->addDefaultHeaders($authenticatedRequest->getHeaders()),
                $this->addDefaultOptions($authenticatedRequest->getOptions())
            );

            if ($response['status'] == 200) {
                $this->authenticationRetryLimit = 0;

                try {
                    return new Response($response['body'], $response['headers']);
                } catch (ResponseException $e) {
                    throw new ClientException($e->getMessage(), $e->getCode(), $e);
                }
            }
        }

        if ($response === null || $response['status'] == 401) {

            $this->authenticationRetryLimit++;

            if ($this->authenticationRetryLimit > self::MAX_RETRY_LIMIT) {
                throw new AccessDeniedException('Authentication retry limit reached.');
            }

            try {
                $this->authenticator->setBaseUrl($request->getBaseUrl());
                $this->authenticator->getAuthenticationTokens();

                // Reexecute event
                return $this->makeApiCall($request);
            } catch (AccessDeniedException $e) {
                throw new AccessDeniedException($e->getMessage(), $e->getCode(), $e);
            } catch (AuthenticationException $e) {
                throw new AccessDeniedException('Could not authenticate against API.', $e->getCode(), $e);
            }
        }

        throw new ClientException(sprintf('The server returned an error with status %s.', $response['status']));
    }

    /**
     * Adds default headers to the headers per request, only if the key
     * cannot not be found in the headers per request.
     *
     * @param array $headers
     *
     * @return array
     */
    private function addDefaultHeaders($headers)
    {
        foreach ($this->headers as $key => $value) {
            if (!isset($headers[$key])) {
                $headers[$key] = $value;
            }
        }

        return $headers;
    }

    /**
     * Merges property options with request options.
     *
     * @param array $options Request options
     *
     * @return array
     */
    private function addDefaultOptions($options)
    {
        return array_merge($options, $this->options);
    }

    /**
     * Sets default options.
     *
     * @param array $options
     *
     * @return self
     */
    public function setOptions(array $options)
    {
        $this->options = $options;

        return $this;
    }

    /**
     * Get options.
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }
}
