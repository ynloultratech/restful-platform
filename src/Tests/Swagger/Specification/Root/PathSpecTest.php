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
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Path\OperationSpec;
use PHPUnit\Framework\TestCase;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Root\PathSpec;

class PathSpecTest extends TestCase
{
    public function testDecorator()
    {
        $path = (new PathSpec('/admin/users', [new OperationSpec('post', [])]))->getDecorator();
        $swObject = new SwaggerObject();
        $path($swObject);

        self::assertNotNull($swObject->getPath('/admin/users')->getOperations()->get('post'));
    }
}
