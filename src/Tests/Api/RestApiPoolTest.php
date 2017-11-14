<?php
/**
 * LICENSE: This file is subject to the terms and conditions defined in
 * file 'LICENSE', which is part of this source code package.
 *
 * @copyright 2017 Copyright(c) - All rights reserved.
 * @author    YNLO-Ultratech Development Team <developer@ynloultratech.com>
 * @package   restful-platform
 */

namespace Ynlo\RestfulPlatformBundle\Tests\Api;

use Ynlo\RestfulPlatformBundle\Tests\Fixtures\Model\Group;
use Ynlo\RestfulPlatformBundle\Tests\Fixtures\Model\User;
use Ynlo\RestfulPlatformBundle\Api\CRUDRestApi;
use Ynlo\RestfulPlatformBundle\Api\RestApiPool;
use PHPUnit\Framework\TestCase;

class RestApiPoolTest extends TestCase
{
    public function testApiPool()
    {
        $pool = new RestApiPool();

        $api1 = new class extends CRUDRestApi
        {
            protected $resourceClass = User::class;
        };
        $pool->addApi($api1);

        $api2 = new class extends CRUDRestApi
        {
            protected $resourceClass = Group::class;
        };
        $pool->addApi($api2);

        self::assertEquals($api1, $pool->getApiByClass(User::class));
        self::assertEquals($api1, $pool->getApiByClass(get_class($api1)));
        self::assertEquals($api2, $pool->getApiByClass(Group::class));
        self::assertNull($pool->getApiByClass(\stdClass::class));
    }
}
