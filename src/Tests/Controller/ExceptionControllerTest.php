<?php

namespace Ynlo\RestfulPlatformBundle\Tests\Controller;

use JMS\Serializer\SerializerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Ynlo\RestfulPlatformBundle\Controller\ExceptionController;
use PHPUnit\Framework\TestCase;

class ExceptionControllerTest extends TestCase
{
    /**
     * @var ContainerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $container;

    protected function setUp()
    {
        $this->container = self::createMock(ContainerInterface::class);

        $serializer = SerializerBuilder::create()->build();
        $this->container->method('get')->with('serializer')->willReturn($serializer);
    }

    public function testExceptionController_HttpException()
    {
        $controller = new ExceptionController();
        $controller->setContainer($this->container);
        $request = new Request();
        $exception = new HttpException(400);

        $response = $controller->showAction($request, $exception);

        self::assertEquals(400, $response->getStatusCode());
        self::assertEquals(['code' => 400, 'message' => 'Bad Request'], json_decode($response->getContent(), true));
    }

    public function testExceptionController_GenericException()
    {
        $controller = new ExceptionController();
        $controller->setContainer($this->container);
        $request = new Request();
        $exception = new \Exception('Foo & Bar');

        $response = $controller->showAction($request, $exception);

        self::assertEquals(500, $response->getStatusCode());
        self::assertEmpty($response->getContent());
    }

    public function testExceptionController_GenericExceptionInDev()
    {
        $controller = new class extends ExceptionController
        {
            protected function getAndCleanOutputBuffering($startObLevel)
            {

            }

            protected function renderView($view, array $parameters = [])
            {

            }
        };
        $controller->setContainer($this->container);
        $request = new Request();
        $exception = new \Exception('Foo & Bar');

        $this->container->method('getParameter')->with('kernel.debug')->willReturn(true);

        $response = $controller->showAction($request, $exception);

        self::assertEquals(500, $response->getStatusCode());
        self::assertEmpty($response->getContent());
    }
}
