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

        $containerMock = \Mockery::mock(ContainerInterface::class)
                                 ->allows('get')
                                 ->andReturn(\Mockery::mock(SerializedNameAnnotationStrategy::class))
                                 ->getMock();
        $bundle->setContainer($containerMock);
        $bundle->boot();
    }
}