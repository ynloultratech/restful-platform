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

use Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema\Model\ModelPropertySchema;
use PHPUnit\Framework\TestCase;

class ModelPropertySchemaTest extends TestCase
{
    public function testName()
    {
        $value = 'some';
        self::assertEquals($value, (new ModelPropertySchema($value))->getName());

        $value = 'name';
        self::assertEquals($value, (new ModelPropertySchema('some'))->setName($value)->getName());
    }

    public function testDescription()
    {
        $value = 'description';
        self::assertEquals($value, (new ModelPropertySchema('name'))->setDescription($value)->getDescription());
    }

    public function testType()
    {
        $value = ModelPropertySchema::TYPE_ARRAY;
        self::assertEquals($value, (new ModelPropertySchema('name'))->setType($value)->getType());
    }

    public function testItemType()
    {
        $value = ModelPropertySchema::TYPE_ARRAY;
        self::assertEquals($value, (new ModelPropertySchema('name'))->setItemType($value)->getItemType());
    }

    public function testKeyType()
    {
        $value = ModelPropertySchema::TYPE_STRING;
        self::assertEquals($value, (new ModelPropertySchema('name'))->setKeyType($value)->getKeyType());
    }

    public function testFormat()
    {
        $value = ModelPropertySchema::FORMAT_FLOAT;
        self::assertEquals($value, (new ModelPropertySchema('name'))->setFormat($value)->getFormat());
    }

    public function testEnum()
    {
        $value = ['pending', 'completed'];
        self::assertEquals($value, (new ModelPropertySchema('name'))->setEnum($value)->getEnum());
    }

    public function testExample()
    {
        $value = 'example';
        self::assertEquals($value, (new ModelPropertySchema('name'))->setExample($value)->getExample());
    }

    public function testRequired()
    {
        $value = true;
        self::assertFalse((new ModelPropertySchema('name'))->isRequired());
        self::assertEquals($value, (new ModelPropertySchema('name'))->setRequired($value)->isRequired());
    }

    public function testReadOnly()
    {
        $value = true;
        self::assertFalse((new ModelPropertySchema('name'))->isReadOnly());
        self::assertEquals($value, (new ModelPropertySchema('name'))->setReadOnly($value)->isReadOnly());
    }

    public function testGroups()
    {
        $value = ['public', 'private'];
        self::assertEquals($value, (new ModelPropertySchema('name'))->setGroups($value)->getGroups());
    }
}
