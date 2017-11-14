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

trait JsonHelperTrait
{
    /**
     * Check if the latest response is a valid JSON
     */
    protected static function assertResponseIsValidJson()
    {
        $response = self::getResponse();

        self::assertNotNull($response);
        self::assertEquals('application/json', $response->headers->get('Content-Type'));
        self::assertJson($response->getContent());
    }

    protected static function getJsonPathValue($path)
    {
        return search($path, json_decode(self::getResponse()->getContent()));
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
}