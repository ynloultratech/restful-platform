<?php

namespace Tests\Swagger\Specification\Common;

use Ynlo\RestfulPlatformBundle\Swagger\Specification\Common\ValueSpec;
use PHPUnit\Framework\TestCase;

class ValueSpecTest extends TestCase
{
    public function testDecorator()
    {
        $spec = self::getMockClass(\stdClass::class);

        $decorator = self::getMockForAbstractClass(ValueSpec::class, ['some_value'], '', true, true, true, ['setValue']);
        $decorator->expects(self::once())->method('setValue')->with($spec, 'some_value');
        $decorator = $decorator->getDecorator();

        //decorate
        $decorator($spec);
    }
}
