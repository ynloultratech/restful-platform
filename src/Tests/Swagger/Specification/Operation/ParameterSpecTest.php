<?php
/**
 * LICENSE: This file is subject to the terms and conditions defined in
 * file 'LICENSE', which is part of this source code package.
 *
 * @copyright 2017 Copyright(c) - All rights reserved.
 * @author    YNLO-Ultratech Development Team <developer@ynloultratech.com>
 * @package   restful-platform
 */

namespace Ynlo\RestfulPlatformBundle\Tests\Swagger\Specification\Operation;

use Ynlo\RestfulPlatformBundle\Swagger\Model\Operation;
use Ynlo\RestfulPlatformBundle\Swagger\Model\Parameter;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Common\CallableSpec;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Operation\ParameterSpec;
use PHPUnit\Framework\TestCase;

class ParameterSpecTest extends TestCase
{
    public function testDecorator()
    {
        $inHeader = new CallableSpec(
            function (Parameter $parameter) {
                $parameter->setIn(Parameter::IN_HEADER);
            }
        );

        $required = new CallableSpec(
            function (Parameter $parameter) {
                $parameter->setRequired(true);
            }
        );

        $spec = new ParameterSpec('some', [$inHeader, $required]);
        $decorator = $spec->getDecorator();

        $operation = new Operation();
        $decorator($operation);

        /** @var Parameter $param */
        $param = $operation->getParameters()->get('some');
        self::assertTrue($param->isRequired());
        self::assertEquals(Parameter::IN_HEADER, $param->getIn());
    }
}
