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

use Ynlo\RestfulPlatformBundle\Tests\Fixtures\Model\User;
use Ynlo\RestfulPlatformBundle\Swagger\Model\Property;
use Ynlo\RestfulPlatformBundle\Swagger\Model\Response;
use Ynlo\RestfulPlatformBundle\Swagger\Model\Schema;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema\NameSpec;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema\PropertySpec;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema\TypeSpec;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\SWSchema;
use PHPUnit\Framework\TestCase;

class SWSchemaTest extends TestCase
{
    public function testModel()
    {
        $decorator = (SWSchema::model(User::class, ['public']))->getDecorator();
        $response = new Response(200);
        $decorator($response);

        self::assertNull($response->getSchema()->getProperty('username'));
        self::assertNotNull($response->getSchema()->getProperty('firstName'));
        self::assertNotNull($response->getSchema()->getProperty('lastName'));
    }

    public function testGroups()
    {
        $decorator = (SWSchema::groups(['public', 'private']))->getDecorator();
        $schema = new Schema('User');
        $decorator($schema);
        self::assertEquals(['public', 'private'], $schema->getGroups());
    }

    public function testMappedClass()
    {
        $decorator = (SWSchema::mappedClass(User::class))->getDecorator();
        $schema = new Schema('User');
        $decorator($schema);
        self::assertEquals(User::class, $schema->getClass());
    }

    public function testSchema()
    {
        $username = new PropertySpec('username', [new TypeSpec('string')]);
        $decorator = (SWSchema::schema('user', [$username]))->getDecorator();
        $response = new Response(200);
        $decorator($response);

        //self::assertEquals('user', $response->getSchema()->getName());
        self::assertNotNull($response->getSchema()->getProperty('username'));
        self::assertEquals('string', $response->getSchema()->getProperty('username')->getType());

        //using array
        $username = new PropertySpec('username', [new TypeSpec('string')]);
        $decorator = (SWSchema::schema([new NameSpec('user'), $username]))->getDecorator();
        $response = new Response(200);
        $decorator($response);

        //self::assertEquals('user', $response->getSchema()->getName());
        self::assertNotNull($response->getSchema()->getProperty('username'));
        self::assertEquals('string', $response->getSchema()->getProperty('username')->getType());
    }

    public function testProperty()
    {
        $decorator = (SWSchema::property('username', 'string', 'datetime', [SWSchema::description('The username')]))->getDecorator();
        $schema = new Schema();
        $decorator($schema);

        self::assertEquals('string', $schema->getProperty('username')->getType());
        self::assertEquals('datetime', $schema->getProperty('username')->getFormat());
        self::assertEquals('The username', $schema->getProperty('username')->getDescription());
    }

    public function testPropertyWithSchema()
    {
        $decorator = (SWSchema::property('user', SWSchema::model(User::class)))->getDecorator();
        $schema = new Schema();
        $decorator($schema);

        self::assertInstanceOf(Schema::class, $schema->getProperty('user'));
        self::assertNotNull($schema->getProperty('user')->getSchema()->getProperty('username'));
    }

    public function testType()
    {
        $decorator = (SWSchema::type('string', 'datetime'))->getDecorator();
        $schema = new Schema('property');
        $decorator($schema);
        self::assertEquals('string', $schema->getType());
        self::assertEquals('datetime', $schema->getFormat());
    }

    public function testDescription()
    {
        $decorator = (SWSchema::description('description'))->getDecorator();
        $schema = new Schema('property');
        $decorator($schema);
        self::assertEquals('description', $schema->getDescription());
    }

    public function testEnum()
    {
        $decorator = (SWSchema::enum(['active', 'inactive']))->getDecorator();
        $schema = new Property('property');
        $decorator($schema);
        self::assertEquals(['active', 'inactive'], $schema->getEnum());
    }

    public function testExample()
    {
        $decorator = (SWSchema::example('example'))->getDecorator();
        $schema = new Schema('property');
        $decorator($schema);
        self::assertEquals('example', $schema->getExample());
    }

    public function testItems()
    {
        $decorator = (SWSchema::items('string'))->getDecorator();
        $schema = new Schema('property');
        $decorator($schema);

        self::assertEquals('string', $schema->getItems()->getType());
    }

    public function testAdditionalProperties()
    {
        $decorator = (SWSchema::additionalProperties('string'))->getDecorator();
        $schema = new Schema('property');
        $decorator($schema);

        self::assertEquals('string', $schema->getAdditionalProperties()->getType());
    }
}
