<?php
/**
 * LICENSE: This file is subject to the terms and conditions defined in
 * file 'LICENSE', which is part of this source code package.
 *
 * @copyright 2017 Copyright(c) - All rights reserved.
 * @author    YNLO-Ultratech Development Team <developer@ynloultratech.com>
 * @package   restful-platform
 */

namespace Ynlo\RestfulPlatformBundle\Tests\Swagger\Specification\Parameter;

use Ynlo\RestfulPlatformBundle\Swagger\Model\Operation;
use Ynlo\RestfulPlatformBundle\Swagger\Model\Parameter;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Operation\ParameterSpec;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Parameter\InSpec;
use PHPUnit\Framework\TestCase;

class InSpecTest extends TestCase
{
    public function testDecorator()
    {
        $in = (new InSpec(Parameter::IN_HEADER));
        $paramDecorator = (new ParameterSpec('param1', [$in]))->getDecorator();
        $operation = new Operation();
        $paramDecorator($operation);

        /** @var Parameter $param1 */
        $param1 = $operation->getParameters()->get('param1');
        self::assertEquals(Parameter::IN_HEADER, $param1->getIn());
    }
}
