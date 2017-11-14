<?php
/**
 * LICENSE: This file is subject to the terms and conditions defined in
 * file 'LICENSE', which is part of this source code package.
 *
 * @copyright 2017 Copyright(c) - All rights reserved.
 * @author    YNLO-Ultratech Development Team <developer@ynloultratech.com>
 * @package   restful-platform
 */

namespace Ynlo\RestfulPlatformBundle\Tests\Util;

use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Naming\CamelCaseNamingStrategy;
use JMS\Serializer\Naming\SerializedNameAnnotationStrategy;
use Ynlo\RestfulPlatformBundle\Tests\Fixtures\Model\Group;
use Ynlo\RestfulPlatformBundle\Tests\Fixtures\Model\User;
use Ynlo\RestfulPlatformBundle\Util\SerializerReader;
use PHPUnit\Framework\TestCase;

class SerializerReaderTest extends TestCase
{
    public function setUp()
    {
        SerializerReader::$namingStrategy = new SerializedNameAnnotationStrategy(new CamelCaseNamingStrategy());
    }

    public function testGetProperties()
    {
        $props = SerializerReader::getProperties(new \ReflectionClass(User::class));
        self::assertEquals(
            [
                'firstName',
                'lastName',
                'username',
                'getFullName',
                'admin',
                'groups',
                'manager',
                'parents',
                'tags',
                'settings',
            ],
            array_keys($props)
        );

        self::assertInstanceOf(\ReflectionProperty::class, $props['username']);
        self::assertInstanceOf(\ReflectionMethod::class, $props['getFullName']);

        //testing from string
        self::assertEquals($props, SerializerReader::getProperties(User::class));

        //testing from instance
        self::assertEquals($props, SerializerReader::getProperties(new User()));
    }

    public function testGetPropertiesWithGroups()
    {
        $props = SerializerReader::getProperties(new \ReflectionClass(User::class), ['public']);
        self::assertEquals(
            [
                'firstName',
                'lastName',
            ],
            array_keys($props)
        );
    }

    public function testGetPropertiesWithExclusionPolicyNone()
    {
        $props = SerializerReader::getProperties(new \ReflectionClass(Group::class));
        self::assertEquals(
            [
                'isBackendAllowed',
                'name',
            ],
            array_keys($props)
        );
    }

    public function testGetSerializedName()
    {
        $props = SerializerReader::getProperties(new \ReflectionClass(User::class));
        self::assertEquals('username', SerializerReader::getSerializedName($props['username']));
        self::assertEquals('full_name', SerializerReader::getSerializedName($props['getFullName']));
        self::assertEquals('first_name', SerializerReader::getSerializedName($props['firstName']));

        SerializerReader::$namingStrategy = null;
        self::assertEquals('getFullName', SerializerReader::getSerializedName($props['getFullName']));
    }

    public function testGetType()
    {
        $props = SerializerReader::getProperties(new \ReflectionClass(User::class));

        //buildIn Type
        $type = SerializerReader::getType($props['username']);
        self::assertEquals('string', $type->getBuiltinType());

        //class Type
        $type = SerializerReader::getType($props['manager']);
        self::assertEquals('object', $type->getBuiltinType());
        self::assertEquals(User::class, $type->getClassName());

        //array
        $type = SerializerReader::getType($props['tags']);
        self::assertEquals('array', $type->getBuiltinType());
        self::assertNull($type->getClassName());
        self::assertTrue($type->isCollection());
        self::assertNull($type->getCollectionKeyType());
        self::assertEquals('string', $type->getCollectionValueType()->getBuiltinType());

        //ArrayCollection
        $type = SerializerReader::getType($props['groups']);
        self::assertEquals('object', $type->getBuiltinType());
        self::assertEquals(ArrayCollection::class, $type->getClassName());
        self::assertTrue($type->isCollection());
        self::assertNull($type->getCollectionKeyType());
        self::assertEquals(Group::class, $type->getCollectionValueType()->getClassName());

        //array with keys
        $type = SerializerReader::getType($props['settings']);
        self::assertEquals('array', $type->getBuiltinType());
        self::assertTrue($type->isCollection());
        self::assertEquals('string', $type->getCollectionKeyType()->getBuiltinType());
        self::assertEquals('string', $type->getCollectionValueType()->getBuiltinType());
    }

    public function testGetSerializerPropertyPath()
    {
        $path = SerializerReader::getSerializedPropertyPath(User::class, 'manager');
        self::assertEquals('manager', $path);

        $path = SerializerReader::getSerializedPropertyPath(User::class, 'manager.firstName');
        self::assertEquals('manager.first_name', $path);

        $path = SerializerReader::getSerializedPropertyPath(new User(), 'manager.lastName');
        self::assertEquals('manager.last_name', $path);

        $path = SerializerReader::getSerializedPropertyPath(User::class, 'manager.groups[0]');
        self::assertEquals('manager.groups[0]', $path);
    }
}
