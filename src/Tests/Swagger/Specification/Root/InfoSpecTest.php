<?php
/**
 * LICENSE: This file is subject to the terms and conditions defined in
 * file 'LICENSE', which is part of this source code package.
 *
 * @copyright 2017 Copyright(c) - All rights reserved.
 * @author    YNLO-Ultratech Development Team <developer@ynloultratech.com>
 * @package   restful-platform
 */

namespace Ynlo\RestfulPlatformBundle\Tests\Swagger\Specification\Root;

use Ynlo\RestfulPlatformBundle\Swagger\Model\SwaggerObject;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Root\InfoSpec;
use PHPUnit\Framework\TestCase;

class InfoSpecTest extends TestCase
{
    public function testDecorator()
    {
        $info = (new InfoSpec('Some API', 'Description', 'v1.0'))->getDecorator();
        $swObject = new SwaggerObject();
        $info($swObject);

        self::assertEquals('Some API', $swObject->getInfo()->getName());
        self::assertEquals('Description', $swObject->getInfo()->getDescription());
        self::assertEquals('v1.0', $swObject->getInfo()->getVersion());
    }
}
