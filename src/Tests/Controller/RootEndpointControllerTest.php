<?php
/**
 * LICENSE: This file is subject to the terms and conditions defined in
 * file 'LICENSE', which is part of this source code package.
 *
 * @copyright 2017 Copyright(c) - All rights reserved.
 * @author    YNLO-Ultratech Development Team <developer@ynloultratech.com>
 * @package   restful-platform
 */

namespace Ynlo\RestfulPlatformBundle\Tests\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Ynlo\RestfulPlatformBundle\Api\CRUDRestApi;
use Ynlo\RestfulPlatformBundle\Api\RestApiInterface;
use Ynlo\RestfulPlatformBundle\Api\RestApiSpecification;
use Ynlo\RestfulPlatformBundle\Controller\RootEndpointController;
use PHPUnit\Framework\TestCase;
use Mockery as m;
use Ynlo\RestfulPlatformBundle\Swagger\Model\Operation;
use Ynlo\RestfulPlatformBundle\Swagger\Model\Parameter;
use Ynlo\RestfulPlatformBundle\Swagger\Model\Path;
use Ynlo\RestfulPlatformBundle\Swagger\Model\SwaggerObject;

class RootEndpointControllerTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @var ContainerInterface|m\MockInterface
     */
    protected $container;

    /**
     * @var RootEndpointController
     */
    protected $controller;

    /**
     * @var RestApiInterface|m\MockInterface
     */
    protected $api;

    protected function setUp()
    {
        $this->container = m::mock(ContainerInterface::class);
        $this->api = m::mock(CRUDRestApi::class);

        $this->controller = new RootEndpointController();
        $this->controller->setContainer($this->container);
        $this->controller->setApi($this->api);
    }

    public function testRootAction()
    {
        $request = m::mock(Request::class);
        $request->shouldReceive('getUri')->andReturn('https://example.com/v1');

        $apiSpec = m::mock(RestApiSpecification::class);

        $apiSpec->shouldReceive('getSpecification')->andReturn($this->buildSwaggerObject());

        $this->container->shouldReceive('get')
                        ->withArgs(['restful_platform.api_specification'])
                        ->andReturn($apiSpec);

        $response = $this->controller->rootAction($request);


        $result = json_decode($response->getContent(), true);
        self::assertEquals($this->expectedResult(), $result);
    }

    protected function buildSwaggerObject()
    {
        $spec = new SwaggerObject();

        $getFoos = new Operation();
        $getFoosParam = new Parameter('q');
        $getFoos->getParameters()->add($getFoosParam);
        $getFoos->setOperationId('foo_list');
        $getFoos->getTags()->add('Foo');

        $createFoo = new Operation();
        $createFoo->setOperationId('foo_create');
        $createFoo->getTags()->add('Foo');

        $foosPath = new Path('/foos');
        $foosPath->getOperations()->set('GET', $getFoos);
        $foosPath->getOperations()->set('POST', $createFoo);

        $getBars = new Operation();
        $getBars->setOperationId('bar_list');
        $getBarsParam = new Parameter('q');
        $getBars->getParameters()->add($getBarsParam);
        $getBars->getTags()->add('Bar');

        $createBar = new Operation();
        $createBar->setOperationId('bar_create');
        $createBar->getTags()->add('Bars');

        $barsPath = new Path('/bars');
        $barsPath->getOperations()->set('GET', $getBars);
        $barsPath->getOperations()->set('POST', $createBar);

        $paths = new ArrayCollection(
            [
                '/foos' => $foosPath,
                '/bars' => $barsPath,
            ]
        );

        $spec->setPaths($paths);

        return $spec;
    }

    protected function expectedResult()
    {
        $json = <<<JSON
{
  "Foo": {
    "list": {
      "href": "https:\/\/example.com\/v1\/foos{?q}",
      "method": "GET"
    },
    "create": {
      "href": "https:\/\/example.com\/v1\/foos",
      "method": "POST"
    }
  },
  "Bar": {
    "list": {
      "href": "https:\/\/example.com\/v1\/bars{?q}",
      "method": "GET"
    }
  },
  "Bars": {
    "create": {
      "href": "https:\/\/example.com\/v1\/bars",
      "method": "POST"
    }
  }
}
JSON;

        return json_decode($json,true);
    }
}
