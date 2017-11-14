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

use function JmesPath\search;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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

    protected static function getJsonPathValue($path)
    {
        return search($path, json_decode(self::getClient()->getResponse()->getContent()));
    }

    protected static function assertJsonPathExist($type, $path)
    {
        self::assertInternalType($type, self::getJsonPathValue($path));
    }

    protected static function assertJsonPathInternalType($type, $path)
    {
        self::assertInternalType($type, self::getJsonPathValue($path));
    }

    protected static function assertJsonPathNotInternalType($type, $path)
    {
        self::assertNotInternalType($type, self::getJsonPathValue($path));
    }

    protected static function assertJsonPathContains($expected, $path)
    {
        self::assertEquals($expected, self::getJsonPathValue($path));
    }

    protected static function assertJsonPathNotContains($expected, $path)
    {
        self::assertNotEquals($expected, self::getJsonPathValue($path));
    }

    protected static function assertJsonPathMatch($path)
    {
        $value = self::getJsonPathValue($path);
        if (is_array($value)) {
            self::assertNotEmpty($value);
        } else {
            self::assertNotNull($value);
        }
    }

    protected static function assertJsonPathNotMatch($path)
    {
        $value = self::getJsonPathValue($path);
        if (is_array($value)) {
            self::assertEmpty($value);
        } else {
            self::assertNull($value);
        }
    }

    protected static function assertResponseCodeIsOK()
    {
        self::assertEquals(Response::HTTP_OK, self::getClient()->getResponse()->getStatusCode());
    }

    protected static function assertResponseCodeIsNotFound()
    {
        self::assertEquals(Response::HTTP_NOT_FOUND, self::getClient()->getResponse()->getStatusCode());
    }

    protected static function assertResponseCodeIsNoContent()
    {
        self::assertEquals(Response::HTTP_NO_CONTENT, self::getClient()->getResponse()->getStatusCode());
    }

    protected static function assertResponseCodeIsCreated()
    {
        self::assertEquals(Response::HTTP_CREATED, self::getClient()->getResponse()->getStatusCode());
    }

    protected static function assertResponseCodeIsForbidden()
    {
        self::assertEquals(Response::HTTP_FORBIDDEN, self::getClient()->getResponse()->getStatusCode());
    }

    protected static function assertResponseCodeIsUnprocessableEntity()
    {
        self::assertEquals(Response::HTTP_UNPROCESSABLE_ENTITY, self::getClient()->getResponse()->getStatusCode());
    }

    /**
     * @return Response
     */
    protected static function getResponse(): Response
    {
        return self::getClient()->getResponse();
    }
}