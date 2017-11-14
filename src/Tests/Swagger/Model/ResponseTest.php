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

use Ynlo\RestfulPlatformBundle\Swagger\Model\Response;
use PHPUnit\Framework\TestCase;

class ResponseTest extends TestCase
{
    public function testCode()
    {
        $value = 200;
        self::assertEquals($value, (new Response($value))->getCode());
        self::assertEquals($value, (new Response(null))->setCode($value)->getCode());
    }
}
