<?php

namespace Ynlo\RestfulPlatformBundle\Tests\Swagger\Model;

use Ynlo\RestfulPlatformBundle\Swagger\Model\Property;
use PHPUnit\Framework\TestCase;

class PropertyTest extends TestCase
{
    public function testEnum()
    {
        $value = ['disabled','enabled'];
        self::assertEquals($value, (new Property())->setEnum($value)->getEnum());
    }
}
