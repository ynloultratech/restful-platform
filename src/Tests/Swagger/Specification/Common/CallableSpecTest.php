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
