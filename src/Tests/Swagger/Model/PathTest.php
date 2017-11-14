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

use Doctrine\Common\Collections\ArrayCollection;
use Ynlo\RestfulPlatformBundle\Swagger\Model\Path;
use PHPUnit\Framework\TestCase;

class PathTest extends TestCase
{
    public function testConstructor()
    {
        $value = '/admin';
        $path = new Path($value);
        self::assertEquals($value, $path->getPath());
        self::assertNotNull($path->getOperations());
    }

    public function testOperations()
    {
        $value = new ArrayCollection();
        self::assertEquals($value, (new Path(''))->setOperations($value)->getOperations());
    }
}
