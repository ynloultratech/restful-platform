<?php

namespace Tests\Api;

use Symfony\Component\Routing\Route;
use Tests\Fixtures\Model\User;
use Ynlo\RestfulPlatformBundle\Api\RestApiInterface;
use Ynlo\RestfulPlatformBundle\Api\RestApiPool;
use Ynlo\RestfulPlatformBundle\Api\RestApiSpecification;
use PHPUnit\Framework\TestCase;
use Mockery as m;
use Ynlo\RestfulPlatformBundle\Routing\ApiRouteCollection;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema\ModelSpec;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\SWOperation;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\SWResponse;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\SWSchema;

class RestApiSpecificationTest extends TestCase
{
    use m\Adapter\Phpunit\MockeryPHPUnitIntegration;

    /**
     * @var m\Mock|RestApiPool
     */
    protected $pool;

    /**
     * @var RestApiSpecification
     */
    protected $spec;

    protected function setUp()
    {
        //remove any existent cache
        @unlink(sys_get_temp_dir().'/api_spec.meta');

        $config = [
            'host' => 'example.com',
            'base_path' => '/v1',
            'documentation' => [
                'info' => [
                    'title' => 'API Title',
                    'description' => 'description',
                    'version' => 'v1',
                ],
                'tags' => [
                    'User' => '',
                    'Group' => [
                        'description' => 'Groups',
                    ],
                    'Foo' => 'Bar',
                ],
            ],
        ];

        $this->pool = m::mock(RestApiPool::class);

        $api = m::mock(RestApiInterface::class);

        $api->shouldReceive('listOperation')->andReturn(
            [
                SWOperation::parameterInQuery('q', 'string'),
                SWOperation::description('User List'),
                SWOperation::response(
                    200,
                    [
                        SWResponse::description('List of users'),
                        SWResponse::model(User::class, ['public']),
                    ]
                ),
            ]
        );

        $api->shouldReceive('createOperation')->andReturn(
            [
                SWOperation::parameterInQuery('q', 'string'),
                SWOperation::body([SWSchema::model(User::class, ['public'])]),
                SWOperation::description('Create User'),
                SWOperation::response(
                    201,
                    [
                        SWResponse::description('Created user'),
                        SWResponse::model(User::class, ['public']),
                    ]
                ),
            ]
        );

        $routes = [
            'user_list' => new Route('', ['_api' => 'UserApi:listOperation'], [], [], null, [], ['GET']),
            'user_create' => new Route('', ['_api' => 'UserApi:createOperation'], [], [], null, [], ['POST']),
        ];
        $routeCollection = m::mock(ApiRouteCollection::class);
        $routeCollection->shouldReceive('getElements')->andReturn($routes);
        $api->shouldReceive('getRoutes')->andReturn($routeCollection);
        $api->shouldReceive('getBaseRouteName')->andReturn('user');
        $api->shouldReceive('getLabel')->andReturn('User');

        $apis = [$api];
        $this->pool->shouldReceive('getElements')->andReturn($apis);

        $this->spec = new RestApiSpecification($this->pool, $config, sys_get_temp_dir());
    }

    protected function tearDown()
    {
        //remove any existent cache
        @unlink(sys_get_temp_dir().'/api_spec.meta');
        ModelSpec::setDescribers([]);
    }

    public function testSerialize()
    {
        $json = $this->spec->serialize();
        self::assertEquals($this->specArray(), json_decode($json, true));

        self::assertFileExists(sys_get_temp_dir().'/api_spec.meta');

        //testing cache
        $pool = m::mock(RestApiPool::class);
        $spec = new RestApiSpecification($pool, [], sys_get_temp_dir());
        $pool->shouldNotReceive('getElements');

        $json = $spec->serialize();
        self::assertEquals($this->specArray(), json_decode($json, true));


        $this->spec->clearCache();
        self::assertFileNotExists(sys_get_temp_dir().'/api_spec.meta');
    }

    public function testGetOperation()
    {
        $op = $this->spec->getOperation('user_list');
        self::assertEquals('user_list', $op->getOperationId());
        self::assertEquals('User', $op->getTags()['User']);

        $op = $this->spec->getOperation('user_delete');
        self::assertNull($op);
    }

    public function testGetRequestBodyClassAndGroups()
    {
        list($class, $groups) = $this->spec->getRequestBodyClassAndGroups('user_create');
        self::assertEquals(User::class, $class);
        self::assertEquals(['public'], $groups);

        $result = $this->spec->getRequestBodyClassAndGroups('user_delete');
        self::assertEquals([null, []], $result);
    }

    public function testGetResponseGroups()
    {
        $groups = $this->spec->getResponseGroups('user_list', 200);
        self::assertEquals(['public'], $groups);

        $op = $this->spec->getResponseGroups('user_delete', 200);
        self::assertEmpty($op);
    }

    protected function specArray()
    {
        $json = <<<JSON
{
  "swagger": "2.0",
  "info": {
    "title": "API Title",
    "version": "v1",
    "description": "description"
  },
  "host": "example.com",
  "basePath": "\/v1",
  "schemes": [],
  "consumes": [
    "application\/json"
  ],
  "produces": [
    "application\/json"
  ],
  "paths": {
    "\/": {
      "get": {
        "operationId": "user_list",
        "tags": [
          "User"
        ],
        "parameters": [
          {
            "in": "query",
            "required": false,
            "name": "q",
            "type": "string"
          }
        ],
        "responses": {
          "200": {
            "description": "List of users",
            "schema": {
              "properties": {
                "firstName": {
                  "type": "string",
                  "example": "John"
                },
                "lastName": {
                  "type": "string",
                  "example": "Smith"
                }
              }
            }
          }
        },
        "description": "User List"
      },
      "post": {
        "operationId": "user_create",
        "tags": [
          "User"
        ],
        "parameters": [
          {
            "in": "query",
            "required": false,
            "name": "q",
            "type": "string"
          },
          {
            "in": "body",
            "required": true,
            "name": "body",
            "schema": {
              "properties": {
                "firstName": {
                  "type": "string",
                  "example": "John"
                },
                "lastName": {
                  "type": "string",
                  "example": "Smith"
                }
              }
            }
          }
        ],
        "responses": {
          "201": {
            "description": "Created user",
            "schema": {
              "properties": {
                "firstName": {
                  "type": "string",
                  "example": "John"
                },
                "lastName": {
                  "type": "string",
                  "example": "Smith"
                }
              }
            }
          }
        },
        "description": "Create User"
      }
    }
  },
  "tags": [
    {
      "name": "Root",
      "description": "Root Endpoint to discover all possible API operations"
    },
    {
      "name": "User"
    },
    {
      "name": "Group",
      "description": "Groups"
    },
     {
      "name": "Foo",
      "description": "Bar"
    }
  ]
}
JSON;

        return json_decode($json, true);
    }
}
