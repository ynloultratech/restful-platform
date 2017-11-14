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
use Ynlo\RestfulPlatformBundle\Api\MediaFileApi;
use PHPUnit\Framework\TestCase;
use Ynlo\RestfulPlatformBundle\Swagger\Model\Operation;
use Ynlo\RestfulPlatformBundle\Swagger\Model\Parameter;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Common\SpecContainer;

class MediaFileApiTest extends TestCase
{
    /**
     * @var MediaFileApi
     */
    protected $api;

    protected function setUp()
    {
        $config['class'] = \stdClass::class;
        $config['path'] = '/attachments';
        $config['actions'] = ['create', 'update'];

        $this->api = new MediaFileApi($config);
    }

    public function testCreateOperation()
    {
        $operationSpecs = new SpecContainer($this->api->createOperation());
        $operation = new Operation();
        $decorator = $operationSpecs->getDecorator();
        $decorator($operation);

        self::assertNotNull($operation->getDescription());

        self::assertEquals(Parameter::IN_PATH, $operation->getParameters()->get('name')->getIn());
        self::assertEquals('string', $operation->getParameters()->get('name')->getType());
        self::assertTrue($operation->getParameters()->get('name')->isRequired());

        self::assertEquals(Parameter::IN_BODY, $operation->getParameters()->get('body')->getIn());
        self::assertEquals('binary', $operation->getParameters()->get('body')->getType());
        self::assertTrue($operation->getParameters()->get('body')->isRequired());

        self::assertEquals(Parameter::IN_HEADER, $operation->getParameters()->get('Content-Type')->getIn());
        self::assertEquals('string', $operation->getParameters()->get('Content-Type')->getType());
        self::assertTrue($operation->getParameters()->get('Content-Type')->isRequired());

        self::assertEquals(Parameter::IN_HEADER, $operation->getParameters()->get('Content-Length')->getIn());
        self::assertEquals('integer', $operation->getParameters()->get('Content-Length')->getType());
        self::assertTrue($operation->getParameters()->get('Content-Length')->isRequired());

        self::assertEquals(Parameter::IN_QUERY, $operation->getParameters()->get('label')->getIn());
        self::assertEquals('string', $operation->getParameters()->get('label')->getType());
        self::assertFalse($operation->getParameters()->get('label')->isRequired());

        self::assertEquals($this->api->getLabel(), $operation->getTags()['stdClass']);

        $successResponse = $operation->getResponse(201);
        self::assertEquals('Successful Operation', $successResponse->getDescription());
        self::assertEquals(\stdClass::class, $successResponse->getSchema()->getClass());

        $errorResponse = $operation->getResponse(400);
        self::assertEquals(
            'Bad Request: Some required parameter is missing, like name or Content-Type',
            $errorResponse->getDescription()
        );

        $errorResponse = $operation->getResponse(502);
        self::assertEquals(
            'Upstream failure',
            $errorResponse->getDescription()
        );
    }

    public function testUpdateOperation()
    {
        $operationSpecs = new SpecContainer($this->api->updateOperation());
        $operation = new Operation();
        $decorator = $operationSpecs->getDecorator();
        $decorator($operation);

        $successResponse = $operation->getResponse(200);
        self::assertEquals('Successful Operation', $successResponse->getDescription());
        self::assertEquals(\stdClass::class, $successResponse->getSchema()->getClass());
    }

    public function testLoadingRequest()
    {
        $request = new Request();
        $request->setMethod(Request::METHOD_POST);

        self::assertNull($this->api->getSubject());

        $this->api->setRequest($request);

        self::assertNotNull($this->api->getSubject());
        self::assertEquals(new \stdClass(), $this->api->getSubject());
    }

    public function testRoutes()
    {
        $routes = $this->api->getRoutes();

        self::assertTrue($routes->has('create'));
        self::assertTrue($routes->has('update'));

        self::assertFalse($routes->has('remove'));
        self::assertFalse($routes->has('list'));

        self::assertEquals($this->api->getBaseRoutePattern().'/{name}', $routes->get('create')->getPath());
        self::assertEquals(Request::METHOD_PUT, $routes->get('update')->getMethods()[0]);
    }
}
