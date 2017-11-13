<?php

namespace Ynlo\RestfulPlatformBundle\Tests\Swagger\Specification\Schema;

use Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema\ReferenceAwareInterface;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema\ReferenceAwareTrait;
use PHPUnit\Framework\TestCase;

class ReferenceAwareTraitTest extends TestCase
{
    public function testDecorator()
    {
        $refAware = new class implements ReferenceAwareInterface
        {
            use ReferenceAwareTrait;
        };
        $value = 'ref';
        self::assertEquals($value, $refAware->setRef($value)->getRef());
    }
}
