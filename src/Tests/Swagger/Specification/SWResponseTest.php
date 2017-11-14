<?php
/**
 * LICENSE: This file is subject to the terms and conditions defined in
 * file 'LICENSE', which is part of this source code package.
 *
 * @copyright 2017 Copyright(c) - All rights reserved.
 * @author    YNLO-Ultratech Development Team <developer@ynloultratech.com>
 * @package   restful-platform
 */

namespace Ynlo\RestfulPlatformBundle\Tests\Swagger\Specification;

use Ynlo\RestfulPlatformBundle\Tests\Fixtures\Model\User;
use Ynlo\RestfulPlatformBundle\Swagger\Model\Response;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema\PropertySpec;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema\TypeSpec;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\SWResponse;
use PHPUnit\Framework\TestCase;

class SWResponseTest extends TestCase
{
    public function testSchema()
    {
        $username = new PropertySpec('username', [new TypeSpec('string')]);
        $decorator = (SWResponse::schema('User', [$username]))->getDecorator();
        $response = new Response(200);
        $decorator($response);

        self::assertEquals('string', $response->getSchema()->getProperty('username')->getType());
    }

    public function testModel()
    {
        $decorator = (SWResponse::model(User::class, ['public']))->getDecorator();
        $response = new Response(200);
        $decorator($response);

        self::assertNull($response->getSchema()->getProperty('username'));
        self::assertNotNull($response->getSchema()->getProperty('firstName'));
    }

    public function testEmptyResponse()
    {
        $decorator = (SWResponse::emptyResponse())->getDecorator();
        $response = new Response(200);
        $decorator($response);

        self::assertNull($response->getSchema());
    }

    public function testDescription()
    {
        $decorator = (SWResponse::description('Success!!!'))->getDecorator();
        $response = new Response(200);
        $decorator($response);

        self::assertEquals('Success!!!', $response->getDescription());
    }
}
