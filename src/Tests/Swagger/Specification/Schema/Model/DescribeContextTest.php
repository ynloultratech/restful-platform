<?php
/**
 * LICENSE: This file is subject to the terms and conditions defined in
 * file 'LICENSE', which is part of this source code package.
 *
 * @copyright 2017 Copyright(c) - All rights reserved.
 * @author    YNLO-Ultratech Development Team <developer@ynloultratech.com>
 * @package   restful-platform
 */

namespace Ynlo\RestfulPlatformBundle\Tests\Swagger\Specification\Schema\Model;

use Ynlo\RestfulPlatformBundle\Tests\Fixtures\Model\User;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema\Model\DescribeContext;
use PHPUnit\Framework\TestCase;

class DescribeContextTest extends TestCase
{
    public function testConstructor()
    {
        $property = new \ReflectionProperty(User::class, 'username');
        $describeContext = new DescribeContext($property, ['public']);
        self::assertEquals($property, $describeContext->getProperty());
        self::assertEquals(['public'], $describeContext->getGroups());
    }
}
