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

use Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema\TypeAwareInterface;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema\TypeAwareTrait;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema\TypeSpec;
use PHPUnit\Framework\TestCase;

class TypeSpecTest extends TestCase
{
    public function testDecorator()
    {
        $value = 'string';
        $decorator = (new TypeSpec($value))->getDecorator();
        $typeAware = new class implements TypeAwareInterface
        {
            use TypeAwareTrait;
        };

        $decorator($typeAware);

        self::assertEquals($value, $typeAware->getType());
    }
}
