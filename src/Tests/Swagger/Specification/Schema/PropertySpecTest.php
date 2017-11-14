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
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema\DescriptionSpec;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema\PropertySpec;
use PHPUnit\Framework\TestCase;

class PropertySpecTest extends TestCase
{
    public function testDecorator()
    {
        $decorator = (new PropertySpec('username', [new DescriptionSpec('description')]))->getDecorator();
        $schema = new Schema();
        $decorator($schema);

        self::assertEquals('description', $schema->getProperty('username')->getDescription());
    }
}
