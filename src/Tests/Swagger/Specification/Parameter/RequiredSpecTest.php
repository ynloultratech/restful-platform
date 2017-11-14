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
use PHPUnit\Framework\TestCase;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Parameter\RequiredSpec;

class RequiredSpecTest extends TestCase
{
    public function testDecorator()
    {
        $required = (new RequiredSpec(true));
        $paramDecorator = (new ParameterSpec('param1', [$required]))->getDecorator();
        $operation = new Operation();
        $paramDecorator($operation);

        /** @var Parameter $param1 */
        $param1 = $operation->getParameters()->get('param1');
        self::assertTrue($param1->isRequired());
    }
}
