<?php

namespace Tests\Swagger\Specification\Parameter;

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
