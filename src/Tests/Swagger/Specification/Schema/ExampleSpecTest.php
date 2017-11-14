<?php
/**
 * LICENSE: This file is subject to the terms and conditions defined in
 * file 'LICENSE', which is part of this source code package.
 *
 * @copyright 2017 Copyright(c) - All rights reserved.
 * @author    YNLO-Ultratech Development Team <developer@ynloultratech.com>
 * @package   restful-platform
 */

namespace Ynlo\RestfulPlatformBundle\Tests\Swagger\Specification\Schema;

use PHPUnit\Framework\TestCase;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema\ExampleAwareInterface;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema\ExampleAwareTrait;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema\ExampleSpec;

class ExampleSpecTest extends TestCase
{
    public function testDecorator()
    {
        $value = 'example';
        $decorator = (new ExampleSpec($value))->getDecorator();

        $exampleAware = new class implements ExampleAwareInterface
        {
            use ExampleAwareTrait;
        };

        $decorator($exampleAware);

        self::assertEquals($value, $exampleAware->getExample());
    }
}
