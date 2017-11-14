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

use Ynlo\RestfulPlatformBundle\Swagger\Model\Schema;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema\GroupsSpec;
use PHPUnit\Framework\TestCase;

class GroupsSpecTest extends TestCase
{
    public function testDecorator()
    {
        $value = ['admin', 'backend'];
        $decorator = (new GroupsSpec($value))->getDecorator();
        $schema = new Schema();
        $decorator($schema);

        self::assertEquals($value, $schema->getGroups());
    }
}
