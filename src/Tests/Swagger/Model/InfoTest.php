<?php
/**
 * LICENSE: This file is subject to the terms and conditions defined in
 * file 'LICENSE', which is part of this source code package.
 *
 * @copyright 2017 Copyright(c) - All rights reserved.
 * @author    YNLO-Ultratech Development Team <developer@ynloultratech.com>
 * @package   restful-platform
 */

namespace Ynlo\RestfulPlatformBundle\Tests\Swagger\Model;

use Ynlo\RestfulPlatformBundle\Swagger\Model\Info;
use PHPUnit\Framework\TestCase;

class InfoTest extends TestCase
{
    public function testVersion()
    {
        $value = 'v1.0';
        self::assertEquals($value, (new Info())->setVersion($value)->getVersion());
    }
}
