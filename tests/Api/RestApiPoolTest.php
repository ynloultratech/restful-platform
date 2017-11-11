<?php

namespace Tests\Api;

use Tests\Fixtures\Model\Group;
use Tests\Fixtures\Model\User;
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
