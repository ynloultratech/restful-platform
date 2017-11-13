<?php

namespace Ynlo\RestfulPlatformBundle\Tests\Swagger\Specification\Schema;

use PHPUnit\Framework\TestCase;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema\ExampleAwareInterface;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema\ExampleAwareTrait;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema\ExampleSpec;

class ExampleSpecTest extends TestCase
{
    public function testDecorator()
    {
        $value = 'example';
        $decorator = (new ExampleSpec($value))->getDecorator();

        $exampleAware = new class implements ExampleAwareInterface
        {
            use ExampleAwareTrait;
        };

        $decorator($exampleAware);

        self::assertEquals($value, $exampleAware->getExample());
    }
}
