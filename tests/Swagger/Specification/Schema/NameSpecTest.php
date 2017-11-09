<?php

namespace Tests\Swagger\Specification\Schema;

use Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema\NameAwareInterface;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema\NameAwareTrait;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema\NameSpec;
use PHPUnit\Framework\TestCase;

class NameSpecTest extends TestCase
{
    public function testDecorator()
    {
        $value = 'name';
        $decorator = (new NameSpec($value))->getDecorator();

        $nameAware = new class implements NameAwareInterface
        {
            use NameAwareTrait;
        };

        $decorator($nameAware);

        self::assertEquals($value, $nameAware->getName());
    }
}
