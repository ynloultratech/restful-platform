<?php
/**
 * LICENSE: This file is subject to the terms and conditions defined in
 * file 'LICENSE', which is part of this source code package.
 *
 * @copyright 2017 Copyright(c) - All rights reserved.
 * @author    YNLO-Ultratech Development Team <developer@ynloultratech.com>
 * @package   restful-platform
 */

namespace Ynlo\RestfulPlatformBundle\Tests\Swagger\Specification\Common;

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
