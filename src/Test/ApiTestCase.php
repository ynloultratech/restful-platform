<?php

/**
 * LICENSE: This file is subject to the terms and conditions defined in
 * file 'LICENSE', which is part of this source code package.
 *
 * @copyright 2017 Copyright(c) - All rights reserved.
 * @author    YNLO-Ultratech Development Team <developer@ynloultratech.com>
 * @package   restful-platform
 */

namespace Ynlo\RestfulPlatformBundle\Test;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Client;

class ApiTestCase extends WebTestCase
{
    private static $client;

    /**
     * @inheritdoc
     */
    protected static function createClient(array $options = [], array $server = [])
    {
        $client = parent::createClient($options, $server);
        self::$client = $client;

        return $client;
    }

    /**
     * @return Client
     */
    protected static function getClient(): Client
    {
        return self::$client ?? self::createClient();
    }

    /**
     * @param string $path
     * @param array  $parameters
     */
    protected static function sendGET($path, array $parameters = [])
    {
        self::getClient()->request(Request::METHOD_GET, $path, $parameters);
    }

    /**
     * Check if the latest response is a valid JSON
     */
    protected static function assertResponseIsValidJson()
    {
        $response = self::getClient()->getResponse();

        self::assertNotNull($response);
        self::assertEquals('application/json', $response->headers->get('Content-Type'));
        self::assertJson($response->getContent());
    }

    /**
     * Check if given status code match with the latest response
     *
     * @param int $code
     */
    protected static function assertResponseCodeIs($code)
    {
        self::assertEquals($code, self::getClient()->getResponse()->getStatusCode());
    }
}