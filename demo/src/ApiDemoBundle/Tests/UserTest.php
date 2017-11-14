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

use Ynlo\RestfulPlatformBundle\Demo\ApiDemoBundle\Entity\User;
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

    public function testUserCreate()
    {
        self::sendPOST('/v1/users', json_encode(['username' => 'david']));

        self::assertResponseCodeIsCreated();
        self::assertRepositoryContains(User::class, ['username' => 'david']);
        self::assertJsonPathContains('david', 'username');
    }

    public function testUserGet()
    {
        self::sendGET('/v1/users/1');

        self::assertResponseCodeIsOK();
        self::assertJsonPathContains('admin', 'username');


        self::sendGET('/v1/users/0');

        self::assertResponseCodeIsNotFound();
        self::assertResponseEmptyContent();
    }

    public function testUserUpdate()
    {
        self::sendGET('/v1/users/1');

        self::assertResponseCodeIsOK();
        self::assertJsonPathContains('admin', 'username');

        $user = self::getResponseJsonArray();
        $user['username'] = 'root';

        self::sendPUT('/v1/users/1', json_encode($user));

        self::assertResponseCodeIsOK();
        self::assertRepositoryContains(User::class, ['id' => 1, 'username' => 'root']);
        self::assertJsonPathContains('root', 'username');
    }

    public function testUserDelete()
    {
        self::sendDELETE('/v1/users/1');

        self::assertResponseCodeIsNoContent();
        self::assertRepositoryNotContains(User::class, ['username' => 'root']);
    }
}