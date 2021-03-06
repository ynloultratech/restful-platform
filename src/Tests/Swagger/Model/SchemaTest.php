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
use Ynlo\RestfulPlatformBundle\Tests\Fixtures\Model\User;
use Ynlo\RestfulPlatformBundle\Swagger\Model\Property;
use Ynlo\RestfulPlatformBundle\Swagger\Model\Schema;
use PHPUnit\Framework\TestCase;

class SchemaTest extends TestCase
{
    public function testProperties()
    {
        $property = new Property();
        $value = new ArrayCollection(['some' => $property]);
        self::assertEquals($value, (new Schema($value))->setProperties($value)->getProperties());
        self::assertEquals($property, (new Schema($value))->setProperties($value)->getProperty('some'));
    }

    public function testItems()
    {
        $value = new Schema();
        self::assertEquals($value, (new Schema($value))->setItems($value)->getItems());
    }

    public function testAdditionalProperties()
    {
        $value = new Schema();
        self::assertEquals($value, (new Schema($value))->setAdditionalProperties($value)->getAdditionalProperties());
        self::assertNotNull($value, (new Schema($value))->getAdditionalProperties());
    }

    public function testClass()
    {
        $value = User::class;
        self::assertEquals($value, (new Schema($value))->setClass($value)->getClass());
    }

    public function testGroups()
    {
        $value = ['public','private'];
        self::assertEquals($value, (new Schema($value))->setGroups($value)->getGroups());
    }
}
