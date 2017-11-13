<?php

namespace Ynlo\RestfulPlatformBundle\Tests\Routing;

use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Ynlo\RestfulPlatformBundle\Api\RestApiInterface;
use Ynlo\RestfulPlatformBundle\Api\RestApiPool;
use PHPUnit\Framework\TestCase;
use Ynlo\RestfulPlatformBundle\Routing\ApiRouteCollection;
use Ynlo\RestfulPlatformBundle\Routing\RouteLoader;

class RouteLoaderTest extends TestCase
{

    public function testLoad()
    {
        $apiPool = self::createMock(RestApiPool::class);
        $api1 = self::createMock(RestApiInterface::class);
        $api2 = self::createMock(RestApiInterface::class);
        $collection1 = self::createMock(ApiRouteCollection::class);
        $collection2 = self::createMock(ApiRouteCollection::class);

        $api1->expects(self::once())->method('getRoutes')->willReturn($collection1);
        $api2->expects(self::once())->method('getRoutes')->willReturn($collection2);

        $routes1 = [
            'user_list' => new Route('/users'),
            'user_get' => new Route('/users/{1}'),
        ];
        $routes2 = [
            'group_list' => new Route('/group'),
            'group_get' => new Route('/group/{1}'),
        ];


        $collection1->expects(self::once())->method('getElements')->willReturn($routes1);
        $collection2->expects(self::once())->method('getElements')->willReturn($routes2);

        $apis = [
            $api1,
            $api2,
        ];

        $apiPool->expects(self::once())->method('getElements')->willReturn($apis);

        $loader = new RouteLoader($apiPool);
        $routes = $loader->load('.', 'restful');

        $expectedRoutes = array_merge($routes1, $routes2);
        $expectedCollection = new RouteCollection();
        foreach ($expectedRoutes as $name => $route) {
            $expectedCollection->add($name, $route);
        }

        self::assertEquals($expectedCollection, $routes);

        self::expectException(\RuntimeException::class);
        self::expectExceptionMessage('Do not add the "api" loader twice');
        $loader->load('.', 'restful');
    }

    public function testSupports()
    {
        $apiPool = self::createMock(RestApiPool::class);
        $loader = new RouteLoader($apiPool);
        self::assertTrue($loader->supports('.', 'restful'));
    }
}
