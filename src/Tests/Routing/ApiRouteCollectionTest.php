<?php

namespace Ynlo\RestfulPlatformBundle\Tests\Routing;

use Symfony\Component\HttpFoundation\Request;
use Ynlo\RestfulPlatformBundle\Api\RestApiInterface;
use Ynlo\RestfulPlatformBundle\Routing\ApiRouteCollection;
use PHPUnit\Framework\TestCase;

class ApiRouteCollectionTest extends TestCase
{
    /**
     * @var RestApiInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $api;

    /**
     * @var ApiRouteCollection
     */
    protected $collection;

    public function setUp()
    {
        $this->api = self::createMock(RestApiInterface::class);
        $this->collection = new ApiRouteCollection($this->api);

        $this->api->method('getBaseRoutePattern')->willReturn('/users');
        $this->api->method('getBaseRouteName')->willReturn('user');
        $this->api->method('getBaseControllerName')->willReturn(\stdClass::class);
    }

    public function testRouteAdd()
    {
        $this->collection->add(Request::METHOD_GET, 'list');
        $this->collection->add(Request::METHOD_GET, 'get', '{id}');
        $this->collection->add(Request::METHOD_POST, 'create', '{id}');

        self::assertEquals(Request::METHOD_GET, $this->collection->get('list')->getMethods()[0]);
        self::assertEquals('/users', $this->collection->get('list')->getPath());

        self::assertEquals(Request::METHOD_GET, $this->collection->get('get')->getMethods()[0]);
        self::assertEquals('/users/{id}', $this->collection->get('get')->getPath());

        self::assertEquals(
            [
                '_controller' => 'stdClass:getAction',
                '_api' => get_class($this->api).':getOperation',
            ],
            $this->collection->get('get')->getDefaults()
        );

        self::assertEquals(Request::METHOD_POST, $this->collection->get('create')->getMethods()[0]);
    }

    public function testGetRouteName()
    {
        self::assertEquals('user_create', $this->collection->getRouteName('create'));
    }

    public function testGetElements()
    {
        $this->collection->add(Request::METHOD_GET, 'list');
        $this->collection->add(Request::METHOD_GET, 'get', '{id}');
        $elements = $this->collection->getElements();

        self::assertEquals('/users', $elements['user_list']->getPath());
        self::assertEquals('/users/{id}', $elements['user_get']->getPath());
    }

    public function testHas()
    {
        $this->collection->add(Request::METHOD_GET, 'list');

        self::assertTrue($this->collection->has('list'));
        self::assertFalse($this->collection->has('create'));
    }

    public function testGet()
    {
        $this->collection->add(Request::METHOD_GET, 'list');

        self::assertEquals('/users', $this->collection->get('list')->getPath());

        self::expectException(\RuntimeException::class);
        self::expectExceptionMessage('Route "create" does not exist.');

        $this->collection->get('create');
    }

    public function testRemove()
    {
        $this->collection->add(Request::METHOD_GET, 'list');

        self::assertTrue($this->collection->has('list'));
        $this->collection->remove('list');
        self::assertFalse($this->collection->has('list'));

        //using array
        $this->collection->add(Request::METHOD_GET, 'list');
        $this->collection->add(Request::METHOD_GET, 'get');

        self::assertTrue($this->collection->has('list'));
        self::assertTrue($this->collection->has('get'));
        $this->collection->remove(['list', 'get']);
        self::assertFalse($this->collection->has('list'));
        self::assertFalse($this->collection->has('get'));
    }

    public function testClearExcept()
    {
        $this->collection->add(Request::METHOD_GET, 'list');
        $this->collection->add(Request::METHOD_GET, 'get');
        $this->collection->add(Request::METHOD_POST, 'create');

        self::assertTrue($this->collection->has('list'));
        self::assertTrue($this->collection->has('get'));
        self::assertTrue($this->collection->has('create'));
        $this->collection->clearExcept('list');
        self::assertTrue($this->collection->has('list'));
        self::assertFalse($this->collection->has('get'));
        self::assertFalse($this->collection->has('create'));

        $this->collection->add(Request::METHOD_GET, 'get');
        $this->collection->add(Request::METHOD_POST, 'create');
        self::assertTrue($this->collection->has('get'));
        self::assertTrue($this->collection->has('create'));
        $this->collection->clearExcept(['list', 'get']);
        self::assertTrue($this->collection->has('list'));
        self::assertTrue($this->collection->has('get'));
        self::assertFalse($this->collection->has('create'));
    }

    public function testClear()
    {
        $this->collection->add(Request::METHOD_GET, 'list');
        $this->collection->add(Request::METHOD_GET, 'get');
        $this->collection->add(Request::METHOD_POST, 'create');

        self::assertTrue($this->collection->has('list'));
        self::assertTrue($this->collection->has('get'));
        self::assertTrue($this->collection->has('create'));
        $this->collection->clear();
        self::assertFalse($this->collection->has('list'));
        self::assertFalse($this->collection->has('get'));
        self::assertFalse($this->collection->has('create'));
    }

    public function testActionify()
    {
        $name = $this->collection->actionify('create');
        self::assertEquals('createAction', $name);
    }
}
