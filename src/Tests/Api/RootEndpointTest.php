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

use Symfony\Component\HttpFoundation\Request;
use Ynlo\RestfulPlatformBundle\Api\RootEndpoint;
use PHPUnit\Framework\TestCase;
use Ynlo\RestfulPlatformBundle\Swagger\Model\Operation;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Common\SpecContainer;

class RootEndpointTest extends TestCase
{
    public function testCreateOperation()
    {
        $operationSpecs = new SpecContainer((new RootEndpoint())->rootOperation());
        $operation = new Operation();
        $decorator = $operationSpecs->getDecorator();
        $decorator($operation);

        self::assertContains(
            'Discover all categories and operations that the REST API supports',
            $operation->getDescription()
        );

        $successResponse = $operation->getResponse(200);
        self::assertContains(
            'Can issue a GET request to the root',
            $successResponse->getDescription()
        );
    }

    public function testRoutes()
    {
        $api = new RootEndpoint();
        $routes = $api->getRoutes();

        self::assertTrue($routes->has('root'));
        self::assertEquals(Request::METHOD_GET, $routes->get('root')->getMethods()[0]);
    }
}
