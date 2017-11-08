<?php

namespace Tests;

use JMS\Serializer\Naming\SerializedNameAnnotationStrategy;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
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
}