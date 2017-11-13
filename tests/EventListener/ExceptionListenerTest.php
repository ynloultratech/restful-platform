<?php

namespace Tests\EventListener;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\HttpKernel\KernelEvents;
use Ynlo\RestfulPlatformBundle\EventListener\ExceptionListener;
use PHPUnit\Framework\TestCase;
use Mockery as m;

class ExceptionListenerTest extends TestCase
{
    use m\Adapter\Phpunit\MockeryPHPUnitIntegration;

    /**
     * @var ExceptionListener
     */
    protected $listener;

    /**
     * @var Controller|m\Mock
     */
    protected $controller;


    protected function setUp()
    {
        $this->controller = m::mock();
        $this->listener = new ExceptionListener($this->controller);
    }

    public function testGetSubscribedEvents()
    {
        self::assertEquals(
            [
                KernelEvents::EXCEPTION => ['onKernelException', -99],
            ],
            $this->listener::getSubscribedEvents()
        );
    }

    public function testOnKernelException()
    {
        $response = new Response();

        $exception = new \Exception();
        $request = m::mock(Request::class);

        $request->shouldReceive('duplicate')->withArgs(
            [
                null,
                null,
                [
                    '_controller' => $this->controller,
                    'exception' => $exception,
                    'logger' => null,
                ],
            ]
        )->andReturnSelf();
        $request->shouldReceive('setMethod')->withArgs(['GET']);

        $kernel = m::mock(Kernel::class);
        $kernel->shouldReceive('handle')->withArgs([$request, HttpKernelInterface::SUB_REQUEST, false])->andReturn($response);

        $event = m::mock(GetResponseForExceptionEvent::class);
        $event->shouldReceive('getException')->andReturn($exception);
        $event->shouldReceive('getRequest')->andReturn($request);
        $event->shouldReceive('getKernel')->andReturn($kernel);
        $event->shouldReceive('setResponse')->withArgs([$response]);

        $this->listener->onKernelException($event);
    }
}
