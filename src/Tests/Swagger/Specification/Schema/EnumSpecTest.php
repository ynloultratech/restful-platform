<?php
/**
 * LICENSE: This file is subject to the terms and conditions defined in
 * file 'LICENSE', which is part of this source code package.
 *
 * @copyright 2017 Copyright(c) - All rights reserved.
 * @author    YNLO-Ultratech Development Team <developer@ynloultratech.com>
 * @package   restful-platform
 */

namespace Ynlo\RestfulPlatformBundle\Tests\Swagger\Specification\Schema;

use Ynlo\RestfulPlatformBundle\Swagger\Model\Property;
use Ynlo\RestfulPlatformBundle\Swagger\Model\Schema;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema\EnumSpec;
use PHPUnit\Framework\TestCase;

class EnumSpecTest extends TestCase
{
    public function testDecorator()
    {
        $value = ['pending', 'completed'];
        $decorator = (new EnumSpec($value))->getDecorator();
        $schema = new Property();
        $decorator($schema);

        self::assertEquals($value, $schema->getEnum());
    }

    public function testDecoratorWithInvalidValue()
    {
        self::expectException(\InvalidArgumentException::class);
        self::expectExceptionMessage('The value should be a array');
        $decorator = (new EnumSpec(''))->getDecorator();
        $decorator(new Property());
    }

    public function testDecoratorWithInvalidType()
    {
        self::expectException(\InvalidArgumentException::class);
        self::expectExceptionMessage(sprintf('Enum only is applicable for "%s"', Property::class));
        $decorator = (new EnumSpec([]))->getDecorator();
        $decorator(new Schema());
    }
}
