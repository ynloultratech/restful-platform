<?php

namespace Tests\Swagger\Specification\Common;

use Ynlo\RestfulPlatformBundle\Swagger\Model\SwaggerSpecModel;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Common\CallableSpec;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Common\SpecContainer;
use PHPUnit\Framework\TestCase;

class SpecContainerTest extends TestCase
{
    public function testDecorator()
    {
        $spec = self::getMockBuilder(SwaggerSpecModel::class)->setMethods(['setName', 'setSize', 'setLabel'])->getMock();
        $spec->expects(self::exactly(2))->method('setName');
        $spec->expects(self::once())->method('setSize');
        $spec->expects(self::once())->method('setLabel');

        $decorator1 = new CallableSpec(
            function ($specification) {
                $specification->setName();
            }
        );

        $decorator2 = new CallableSpec(
            function ($specification) {
                $specification->setSize();
            }
        );

        $decorator3 = new CallableSpec(
            function ($specification) {
                $specification->setLabel();
            }
        );

        //support array of specs and sub-arrays
        $specContainer = new SpecContainer([$decorator1, $decorator2, [$decorator1, $decorator3]]);
        $decorator = $specContainer->getDecorator();

        //decorate
        $decorator($spec);
    }
}
