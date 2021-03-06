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

use Symfony\Bridge\Twig\TwigEngine;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Ynlo\RestfulPlatformBundle\Api\RestApiSpecification;
use Ynlo\RestfulPlatformBundle\Controller\DocumentationController;
use PHPUnit\Framework\TestCase;

class DocumentationControllerTest extends TestCase
{
    /**
     * @var ContainerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $container;

    protected function setUp()
    {
        $this->container = self::createMock(ContainerInterface::class);
    }

    public function testDocAction()
    {
        $controller = new DocumentationController();
        $controller->setContainer($this->container);

        $request = new Request();
        $request->query->set('version', 'v1');

        $this->container->expects(self::once())
                        ->method('getParameter')
                        ->with('restful_platform.config')
                        ->willReturn(
                            [
                                'documentation' => ['info' => ['title' => 'API Title']],
                            ]
                        );

        $templating = self::createMock(TwigEngine::class);
        $templating->expects(self::once())
                   ->method('render')->with(
                '@RestfulPlatform/doc.html.twig',
                [
                    'version' => $request->get('version'),
                    'title' => 'API Title',
                ]
            );

        $this->container->expects(self::once())
                        ->method('has')
                        ->with('templating')
                        ->willReturn(true);

        $this->container->expects(self::once())
                        ->method('get')
                        ->with('templating')
                        ->willReturn($templating);

        $controller->docAction($request);
    }

    public function testDocJsonAction()
    {
        $controller = new DocumentationController();
        $controller->setContainer($this->container);

        $json = '{"api":"json"}';

        $spec = self::createMock(RestApiSpecification::class);
        $spec->method('serialize')->willReturn($json);

        $this->container->expects(self::once())
                        ->method('get')
                        ->with('restful_platform.api_specification')
                        ->willReturn($spec);

        $response = $controller->docJsonAction(new Request());
        self::assertEquals(200, $response->getStatusCode());
        self::assertEquals($json, $response->getContent());
    }

    public function testDocJsonAction_WithVersion()
    {
        $controller = new DocumentationController();
        $controller->setContainer($this->container);

        $json = '{"api":"json", "basePath": "\/{version}"}';
        $jsonResult = '{"api":"json","basePath":"\/v1"}';

        $spec = self::createMock(RestApiSpecification::class);
        $spec->method('serialize')->willReturn($json);

        $this->container->expects(self::once())
                        ->method('get')
                        ->with('restful_platform.api_specification')
                        ->willReturn($spec);

        $response = $controller->docJsonAction(new Request(['version' => 'v1']));
        self::assertEquals(200, $response->getStatusCode());
        self::assertEquals($jsonResult, $response->getContent());
    }
}
