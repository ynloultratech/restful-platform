<?php
/**
 * LICENSE: This file is subject to the terms and conditions defined in
 * file 'LICENSE', which is part of this source code package.
 *
 * @copyright 2017 Copyright(c) - All rights reserved.
 * @author    YNLO-Ultratech Development Team <developer@ynloultratech.com>
 * @package   restful-platform
 */

namespace Ynlo\RestfulPlatformBundle\Demo\ApiDemoBundle\Tests;

use Ynlo\RestfulPlatformBundle\Test\ApiTestCase;

class UserTest extends ApiTestCase
{
    public function testUserList()
    {
        self::sendGET('/v1/users');

        self::assertResponseCodeIsOK();

        self::assertJsonPathInternalType('integer', 'pages');
        self::assertJsonPathInternalType('integer', 'total');
        self::assertJsonPathInternalType('integer', 'limit');
        self::assertJsonPathInternalType('array', 'items');

        self::assertJsonPathContains(1, 'pages');
        self::assertJsonPathContains(4, 'total');
        self::assertJsonPathContains(30, 'limit');

        self::assertJsonPathContains(['admin', 'edward', 'darren', 'kevin'], 'items[*].username');
    }
}