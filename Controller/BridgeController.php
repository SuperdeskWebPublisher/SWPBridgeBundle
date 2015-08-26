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

namespace SWP\BridgeBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use SWP\BridgeBundle\Client\GuzzleClient;
use Superdesk\ContentApiSdk\ContentApiSdk;
use Superdesk\ContentApiSdk\Client\ClientInterface;
use Superdesk\ContentApiSdk\Exception\ContentApiException;

/**
 * @Route("/bridge")
 */
class BridgeController extends Controller
{
    /**
     * @Route("/{endpoint}/")
     * @Route("/{endpoint}/{objectId}/")
     * @Method("GET")
     *
     * Indexaction for bridge controller
     *
     * @param Request     $request
     * @param string      $endpoint Endpoint of the api
     * @param string|null $objectId Identifier of object to retrieve
     *
     * @return Response
     */
    public function indexAction(Request $request, $endpoint, $objectId = null)
    {
        $data = array();
        $client = $this->getClient();
        $sdk = $this->getSDK($client);
        $parameters = $request->query->all();
        $endpointPath = sprintf('/%s', $endpoint);

        if ($this->isValidEndpoint($endpointPath)) {
            throw new ContentApiException(sprintf('Endpoint %s not supported.', $endpoint));
        }

        switch ($endpointPath) {
            case $sdk::SUPERDESK_ENDPOINT_ITEMS:

                if (!is_null($objectId)) {
                    $data = $sdk->getItem($objectId);
                } else {
                    $data = $sdk->getItems($parameters);
                }
                break;

            case $sdk::SUPERDESK_ENDPOINT_PACKAGES:

                // TODO: Change this in the future to match the superdesk public api parameter name
                $resolve = (isset($parameters['resolveItems']) && $parameters['resolveItems']) ? true : false;
                unset($parameters['resolveItems']);

                if (!is_null($objectId)) {
                    $data = $sdk->getPackage($objectId, $resolve);
                } else {
                    $data = $sdk->getPackages($parameters, $resolve);
                }
                break;
        }

        return $this->render('SWPBridgeBundle:Default:data_dump.html.twig', array('data' => $data));
    }

    /**
     * Get an instance of the sdk
     *
     * @param  ClientInterface $client HTTP Client
     *
     * @return \Superdesk\ContentApiSdk\ContentApiSdk
     */
    private function getSDK(ClientInterface $client)
    {
        return new ContentApiSdk($client);
    }

    /**
     * Get an instance of the HTTP client. The returned class should implement
     * the \Superdesk\ContentApiSdk\Client\ClientInterface interface.
     *
     * @return GuzzleClient
     */
    private function getClient()
    {
        $bridgeConfig = array(
            'base_uri' => $this->container->getParameter('swp_bridge.base_uri'),
            'options' => $this->container->getParameter('swp_bridge.options'),
        );

        new GuzzleClient($bridgeConfig);
    }

    /**
     * Check if the supplied endpoint is supported by the SDK.
     *
     * @param  string  $endpoint Endpoint url (/ will be automatically prepended)
     *
     * @return boolean
     */
    private function isValidEndpoint($endpoint)
    {
        return (!in_array(sprintf('/%s', ltrim($endpointPath, '/')), ContentApiSdk::getAvailableEndpoints()));
    }
}
