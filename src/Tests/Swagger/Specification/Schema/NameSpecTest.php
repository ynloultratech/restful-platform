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

use Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema\NameAwareInterface;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema\NameAwareTrait;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema\NameSpec;
use PHPUnit\Framework\TestCase;

class NameSpecTest extends TestCase
{
    public function testDecorator()
    {
        $value = 'name';
        $decorator = (new NameSpec($value))->getDecorator();

        $nameAware = new class implements NameAwareInterface
        {
            use NameAwareTrait;
        };

        $decorator($nameAware);

        self::assertEquals($value, $nameAware->getName());
    }
}
