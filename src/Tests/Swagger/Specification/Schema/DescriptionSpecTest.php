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

use Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema\DescriptionAwareInterface;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema\DescriptionAwareTrait;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema\DescriptionSpec;
use PHPUnit\Framework\TestCase;

class DescriptionSpecTest extends TestCase
{
    public function testDecorator()
    {
        $value = 'description';
        $decorator = (new DescriptionSpec($value))->getDecorator();

        $descriptionAware = new class implements DescriptionAwareInterface
        {
            use DescriptionAwareTrait;
        };

        $decorator($descriptionAware);

        self::assertEquals($value, $descriptionAware->getDescription());
    }
}
