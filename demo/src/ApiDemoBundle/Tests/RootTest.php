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

use Symfony\Component\HttpFoundation\Response;
use Ynlo\RestfulPlatformBundle\Test\ApiTestCase;

class RootTest extends ApiTestCase
{
    public function testRoot()
    {
        self::sendGET('/v1/');
        self::assertResponseCodeIs(Response::HTTP_OK);
        self::assertResponseIsValidJson();
        self::assertJsonStringEqualsJsonFile(__DIR__.'/root.json', self::getResponse()->getContent());
    }
}