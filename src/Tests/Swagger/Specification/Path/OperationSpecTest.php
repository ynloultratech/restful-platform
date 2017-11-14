<?php
/**
 * LICENSE: This file is subject to the terms and conditions defined in
 * file 'LICENSE', which is part of this source code package.
 *
 * @copyright 2017 Copyright(c) - All rights reserved.
 * @author    YNLO-Ultratech Development Team <developer@ynloultratech.com>
 * @package   restful-platform
 */

namespace Ynlo\RestfulPlatformBundle\Tests\Swagger\Specification\Path;

use Ynlo\RestfulPlatformBundle\Swagger\Model\Operation;
use Ynlo\RestfulPlatformBundle\Swagger\Model\Path;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Operation\TagSpec;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Path\OperationSpec;
use PHPUnit\Framework\TestCase;

class OperationSpecTest extends TestCase
{
    public function testDecorator()
    {
        $operation = (new OperationSpec('post', [new TagSpec('backend')]))->getDecorator();
        $path = new Path('/admin/users');
        $operation($path);

        /** @var Operation $operation1 */
        $operation1 = $path->getOperations()->get('post');
        self::assertEquals('backend', $operation1->getTags()->first());
    }
}
