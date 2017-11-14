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
use Ynlo\RestfulPlatformBundle\Component\TaggedServices\TagSpecification;
use PHPUnit\Framework\TestCase;

class TagSpecificationTest extends TestCase
{
    public function testTagSpecification()
    {
        $service = new \stdClass();

        $container = self::createMock(ContainerInterface::class);
        $container->expects(self::once())->method('get')->with('id')->willReturn($service);

        $tagSpecification = new TagSpecification('id', 'name', ['priority' => 255], $container);

        self::assertEquals('id', $tagSpecification->getId());
        self::assertEquals('name', $tagSpecification->getName());
        self::assertEquals(['priority' => 255], $tagSpecification->getAttributes());
        self::assertEquals($service, $tagSpecification->getService());
    }
}
