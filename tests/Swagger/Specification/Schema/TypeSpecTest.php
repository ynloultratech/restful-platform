<?php

namespace Tests\Swagger\Specification\Schema;

use Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema\TypeAwareInterface;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema\TypeAwareTrait;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema\TypeSpec;
use PHPUnit\Framework\TestCase;

class TypeSpecTest extends TestCase
{
    public function testDecorator()
    {
        $value = 'string';
        $decorator = (new TypeSpec($value))->getDecorator();
        $typeAware = new class implements TypeAwareInterface
        {
            use TypeAwareTrait;
        };

        $decorator($typeAware);

        self::assertEquals($value, $typeAware->getType());
    }
}
