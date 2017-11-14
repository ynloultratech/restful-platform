<?php
/**
 * LICENSE: This file is subject to the terms and conditions defined in
 * file 'LICENSE', which is part of this source code package.
 *
 * @copyright 2017 Copyright(c) - All rights reserved.
 * @author    YNLO-Ultratech Development Team <developer@ynloultratech.com>
 * @package   restful-platform
 */

namespace Ynlo\RestfulPlatformBundle\Tests;

use JMS\Serializer\Naming\SerializedNameAnnotationStrategy;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Ynlo\RestfulPlatformBundle\Api\RestApiInterface;
use Ynlo\RestfulPlatformBundle\RestfulPlatformBundle;

class RestfulPlatformBundleTest extends TestCase
{
    public function testBundleConstructor()
    {
        $bundle = new RestfulPlatformBundle();
        self::assertEquals('RestfulPlatformBundle', $bundle->getName());

        $containerMock = self::createMock(ContainerInterface::class);
        $containerMock->expects(self::once())
                      ->method('get')
                      ->willReturn(self::createMock(SerializedNameAnnotationStrategy::class));

        $bundle->setContainer($containerMock);
        $bundle->boot();
    }

    public function testBuild()
    {
        /** @var ContainerBuilder|\PHPUnit_Framework_MockObject_MockObject $container */
        $container = self::createMock(ContainerBuilder::class);

        $childDefinition = self::createMock(ChildDefinition::class);
        $childDefinition->expects(self::once())->method('addTag')->with('restful_platform.rest_api');
        $container->expects(self::once())->method('registerForAutoconfiguration')->with(RestApiInterface::class)->willReturn($childDefinition);

        $bundle = new RestfulPlatformBundle();
        $bundle->build($container);
    }
}