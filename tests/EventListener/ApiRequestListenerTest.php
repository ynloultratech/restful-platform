<?php

namespace Tests\EventListener;

use JMS\Serializer\Context;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Tests\Fixtures\Model\User;
use Ynlo\RestfulPlatformBundle\Api\RestApiInterface;
use Ynlo\RestfulPlatformBundle\Api\RestApiSpecification;
use Ynlo\RestfulPlatformBundle\Controller\RestApiControllerInterface;
use Ynlo\RestfulPlatformBundle\EventListener\ApiRequestListener;
use PHPUnit\Framework\TestCase;
use Mockery as m;
use Ynlo\RestfulPlatformBundle\MediaServer\MediaStorageProviderPool;

class ApiRequestListenerTest extends TestCase
{
    use m\Adapter\Phpunit\MockeryPHPUnitIntegration;

    /**
     * @var ContainerInterface|m\MockInterface
     */
    protected $container;

    /**
     * @var ApiRequestListener
     */
    protected $listener;

    protected function setUp()
    {
        $this->container = m::mock(ContainerInterface::class);

        $this->listener = new ApiRequestListener();
        $this->listener->setContainer($this->container);
    }

    public function testGetSubscribedEvents()
    {
        self::assertEquals(
            [
                KernelEvents::VIEW => ['onKernelView', 30],
                KernelEvents::CONTROLLER => ['onController'],
            ],
            $this->listener::getSubscribedEvents()
        );
    }

    public function testOnController()
    {
        $request = m::mock(Request::class);
        $request->shouldReceive('get')->withArgs(['_api'])->andReturn('\Foo\Bar\ApiClass::list');

        $controller = m::mock(RestApiControllerInterface::class);

        $event = m::mock(FilterControllerEvent::class);
        $event->shouldReceive('getRequest')->andReturn($request);
        $event->shouldReceive('getController')->andReturn([$controller, 'action']);

        $api = m::mock(RestApiInterface::class);
        $api->shouldReceive('setContainer')->withArgs([$this->container]);
        $api->shouldReceive('setRequest')->withArgs([$request]);

        $pool = m::mock(MediaStorageProviderPool::class);
        $pool->shouldReceive('getApiByClass')->withArgs(['\Foo\Bar\ApiClass'])->andReturn($api);

        $this->container->shouldReceive('get')
                        ->withArgs(['restful_platform.api_pool'])
                        ->andReturn($pool);

        $controller->shouldReceive('setApi')->withArgs([$api]);

        $this->listener->onController($event);
    }

    public function testOnKernelView_WithResponseInstance()
    {
        $request = m::mock(Request::class);
        $request->shouldReceive('get')->withArgs(['_api'])->andReturn('\Foo\Bar\ApiClass::list');

        $event = m::mock(GetResponseForControllerResultEvent::class);
        $event->shouldReceive('getRequest')->andReturn($request);
        $event->shouldReceive('getControllerResult')->andReturn(new Response());

        $event->shouldNotReceive('setResponse');

        $this->listener->onKernelView($event);
    }

    public function testOnKernelView_WithOnlyStatusCode()
    {
        $request = m::mock(Request::class);
        $request->shouldReceive('get')->withArgs(['_api'])->andReturn('\Foo\Bar\ApiClass::list');

        $event = m::mock(GetResponseForControllerResultEvent::class);
        $event->shouldReceive('getRequest')->andReturn($request);
        $event->shouldReceive('getControllerResult')->andReturn(200);

        $event->shouldReceive('setResponse')->withArgs(
            function (Response $response) {
                self::assertEquals(200, $response->getStatusCode());
                self::assertEquals('', $response->getContent());

                return true;
            }
        );

        $this->listener->onKernelView($event);
    }

    public function testOnKernelView_WithStatusCodeAndObject()
    {
        $object = new \stdClass();

        $request = m::mock(Request::class);
        $request->shouldReceive('get')->withArgs(['_api'])->andReturn('\Foo\Bar\ApiClass::list');
        $request->shouldReceive('get')->withArgs(['_route'])->andReturn('route_name');

        $event = m::mock(GetResponseForControllerResultEvent::class);
        $event->shouldReceive('getRequest')->andReturn($request);
        $event->shouldReceive('getControllerResult')->andReturn([200, $object]);

        $event->shouldReceive('setResponse')->withArgs(
            function (Response $response) {
                self::assertEquals(200, $response->getStatusCode());
                self::assertEquals('{}', $response->getContent());

                return true;
            }
        );

        $groups = ['foo', 'bar'];
        $apiSpec = m::mock(RestApiSpecification::class);
        $apiSpec->shouldReceive('getResponseGroups')->withArgs(['route_name', 200])->andReturn($groups);

        $this->container->shouldReceive('get')
                        ->withArgs(['restful_platform.api_specification'])
                        ->andReturn($apiSpec);

        $serializer = m::mock(SerializerInterface::class);
        $serializer->shouldReceive('serialize')->withArgs(
            function ($data, $format, SerializationContext $context) use ($object, $groups) {
                self::assertEquals($object, $data);
                self::assertEquals('json', $format);
                self::assertEquals($groups, $context->attributes->get('groups')->get());

                return true;
            }
        )->andReturn('{}');

        $this->container->shouldReceive('get')
                        ->withArgs(['serializer'])
                        ->andReturn($serializer);

        $this->listener->onKernelView($event);
    }

    public function testOnKernelView_WithStatusCodeAndObjectAndHeader()
    {
        $object = new \stdClass();

        $request = m::mock(Request::class);
        $request->shouldReceive('get')->withArgs(['_api'])->andReturn('\Foo\Bar\ApiClass::list');
        $request->shouldReceive('get')->withArgs(['_route'])->andReturn('route_name');

        $event = m::mock(GetResponseForControllerResultEvent::class);
        $event->shouldReceive('getRequest')->andReturn($request);
        $event->shouldReceive('getControllerResult')->andReturn([200, $object, ['X-Foo' => 'Bar']]);

        $event->shouldReceive('setResponse')->withArgs(
            function (Response $response) {
                self::assertEquals(200, $response->getStatusCode());
                self::assertEquals('{}', $response->getContent());
                self::assertEquals('Bar', $response->headers->get('X-Foo'));

                return true;
            }
        );

        $groups = ['foo', 'bar'];
        $apiSpec = m::mock(RestApiSpecification::class);
        $apiSpec->shouldReceive('getResponseGroups')->withArgs(['route_name', 200])->andReturn($groups);

        $this->container->shouldReceive('get')
                        ->withArgs(['restful_platform.api_specification'])
                        ->andReturn($apiSpec);

        $serializer = m::mock(SerializerInterface::class);
        $serializer->shouldReceive('serialize')->withArgs(
            function ($data, $format, SerializationContext $context) use ($object, $groups) {
                self::assertEquals($object, $data);
                self::assertEquals('json', $format);
                self::assertEquals($groups, $context->attributes->get('groups')->get());

                return true;
            }
        )->andReturn('{}');

        $this->container->shouldReceive('get')
                        ->withArgs(['serializer'])
                        ->andReturn($serializer);

        $this->listener->onKernelView($event);
    }
}
