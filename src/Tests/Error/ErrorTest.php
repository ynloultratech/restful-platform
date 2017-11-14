<?php
/**
 * LICENSE: This file is subject to the terms and conditions defined in
 * file 'LICENSE', which is part of this source code package.
 *
 * @copyright 2017 Copyright(c) - All rights reserved.
 * @author    YNLO-Ultratech Development Team <developer@ynloultratech.com>
 * @package   restful-platform
 */

namespace Ynlo\RestfulPlatformBundle\Tests\Error;

use Ynlo\RestfulPlatformBundle\Error\Error;
use PHPUnit\Framework\TestCase;

class ErrorTest extends TestCase
{
    public function testConstructor()
    {
        $error = new Error(200, 'error');
        self::assertEquals(200, $error->getCode());
        self::assertEquals('error', $error->getMessage());
    }
}
