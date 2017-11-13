<?php

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
