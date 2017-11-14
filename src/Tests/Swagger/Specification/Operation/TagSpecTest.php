<?php
/**
 * LICENSE: This file is subject to the terms and conditions defined in
 * file 'LICENSE', which is part of this source code package.
 *
 * @copyright 2017 Copyright(c) - All rights reserved.
 * @author    YNLO-Ultratech Development Team <developer@ynloultratech.com>
 * @package   restful-platform
 */

namespace Ynlo\RestfulPlatformBundle\Tests\Swagger\Specification\Operation;

use Ynlo\RestfulPlatformBundle\Swagger\Model\Operation;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Operation\TagSpec;
use PHPUnit\Framework\TestCase;

class TagSpecTest extends TestCase
{
    public function testDecorator()
    {
        $tag1 = (new TagSpec('books'))->getDecorator();
        $tag2 = (new TagSpec('backend'))->getDecorator();

        $operation = new Operation();

        $tag1($operation);
        $tag2($operation);

        self::assertEquals(2, $operation->getTags()->count());
        self::assertEquals('books', $operation->getTags()->first());
        self::assertEquals('backend', $operation->getTags()->last());
    }
}
