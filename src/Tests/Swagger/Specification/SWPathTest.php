<?php
/**
 * LICENSE: This file is subject to the terms and conditions defined in
 * file 'LICENSE', which is part of this source code package.
 *
 * @copyright 2017 Copyright(c) - All rights reserved.
 * @author    YNLO-Ultratech Development Team <developer@ynloultratech.com>
 * @package   restful-platform
 */

namespace Ynlo\RestfulPlatformBundle\Tests\Swagger\Specification;

use Ynlo\RestfulPlatformBundle\Swagger\Model\Path;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\SWOperation;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\SWPath;
use PHPUnit\Framework\TestCase;

class SWPathTest extends TestCase
{
    /**
     * @dataProvider methods
     */
    public function testMethods($method)
    {
        $decorator = (SWPath::$method([SWOperation::operationId('operation')]))->getDecorator();
        $path = new Path('/admin/users');
        $decorator($path);

        self::assertEquals('operation', $path->getOperations()->get($method)->getOperationId());
    }

    public function methods()
    {
        return [
            ['get'],
            ['post'],
            ['put'],
            ['delete'],
            ['patch'],
            ['options'],
            ['head'],
        ];
    }
}
