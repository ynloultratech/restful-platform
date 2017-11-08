<?php

namespace Tests\Swagger\Specification\Common;

use Ynlo\RestfulPlatformBundle\Swagger\Specification\Common\CallableSpec;
use PHPUnit\Framework\TestCase;

class CallableSpecTest extends TestCase
{
    public function testDecorator()
    {
        $double = self::getMockBuilder(\stdClass::class)->setMethods(['createSpec'])->getMock();
        $double->expects(self::once())->method('createSpec');
        $callback = function ($specification) {
            $specification->createSpec();
        };
        $callableSpec = new CallableSpec($callback);
        $decorator = $callableSpec->getDecorator();

        //decorate
        $decorator($double);
    }
}
