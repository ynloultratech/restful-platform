<?php
/**
 * LICENSE: This file is subject to the terms and conditions defined in
 * file 'LICENSE', which is part of this source code package.
 *
 * @copyright 2017 Copyright(c) - All rights reserved.
 * @author    YNLO-Ultratech Development Team <developer@ynloultratech.com>
 * @package   restful-platform
 */

namespace Ynlo\RestfulPlatformBundle\Tests\Component\TaggedServices;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Ynlo\RestfulPlatformBundle\Component\TaggedServices\TaggedServices;
use PHPUnit\Framework\TestCase;

class TaggedServicesTest extends TestCase
{
    public function testTaggedServices()
    {
        $mock = self::createMock(ContainerInterface::class);
        $taggedServices = new TaggedServices($mock);

        $taggedServices->addSpecification('some_service', 'some_tag', ['priority' => 255]);
        $taggedServices->addSpecification('another_service', 'some_tag');
        $taggedServices->addSpecification('and_another_service', 'another_tag');

        $services = $taggedServices->findTaggedServices('some_tag');

        self::assertCount(2, $services);
        self::assertEquals('some_service', $services[0]->getId());
        self::assertEquals(['priority' => 255], $services[0]->getAttributes());
        self::assertEquals('another_service', $services[1]->getId());

        self::assertCount(1, $taggedServices->findTaggedServices('another_tag'));
        self::assertCount(0, $taggedServices->findTaggedServices('tag'));
    }
}
