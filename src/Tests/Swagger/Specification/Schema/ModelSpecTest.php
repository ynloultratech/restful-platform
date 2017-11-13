<?php

namespace Ynlo\RestfulPlatformBundle\Tests\Swagger\Specification\Schema;

use JMS\Serializer\Naming\CamelCaseNamingStrategy;
use JMS\Serializer\Naming\SerializedNameAnnotationStrategy;
use Ynlo\RestfulPlatformBundle\Tests\Fixtures\Model\Group;
use Ynlo\RestfulPlatformBundle\Tests\Fixtures\Model\User;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema\Model\Describer\DocCommentDescriber;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema\Model\Describer\DoctrineDescriber;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema\Model\Describer\ExampleDescriber;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema\Model\Describer\JMSSerializerDescriber;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema\Model\Describer\SwaggerAnnotationsDescriber;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema\Model\Describer\SymfonyValidatorDescriber;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema\Model\ModelPropertySchema;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema\ModelSpec;
use PHPUnit\Framework\TestCase;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema\SchemaAwareInterface;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema\SchemaAwareTrait;
use Ynlo\RestfulPlatformBundle\Util\SerializerReader;

class ModelSpecTest extends TestCase
{

    public function setUp()
    {
        SerializerReader::$namingStrategy = new SerializedNameAnnotationStrategy(new CamelCaseNamingStrategy());
        ModelSpec::addDescriber(new SwaggerAnnotationsDescriber());
        ModelSpec::addDescriber(new JMSSerializerDescriber());
        ModelSpec::addDescriber(new DoctrineDescriber());
        ModelSpec::addDescriber(new SymfonyValidatorDescriber());
        ModelSpec::addDescriber(new DocCommentDescriber());
        ModelSpec::addDescriber(new ExampleDescriber());
    }

    public function testDecorator()
    {
        $decorator = (new ModelSpec(User::class, [], 2))->getDecorator();
        $schemaAware = new class implements SchemaAwareInterface
        {
            use SchemaAwareTrait;
        };
        $decorator($schemaAware);

        $model = $schemaAware->getSchema();
        self::assertCount(10, $model->getProperties());

        //username
        self::assertEquals(ModelPropertySchema::TYPE_STRING, $model->getProperty('username')->getType());
        self::assertEquals('admin', $model->getProperty('username')->getExample());

        //firstName
        self::assertEquals(ModelPropertySchema::TYPE_STRING, $model->getProperty('first_name')->getType());
        self::assertEquals('John', $model->getProperty('first_name')->getExample());
        self::assertEquals(['public'], $model->getProperty('first_name')->getGroups());

        //lastName
        self::assertEquals(ModelPropertySchema::TYPE_STRING, $model->getProperty('last_name')->getType());
        self::assertEquals('Smith', $model->getProperty('last_name')->getExample());
        self::assertEquals(['public'], $model->getProperty('last_name')->getGroups());

        //manager
        self::assertNull($model->getProperty('manager')->getType());
        self::assertEquals(User::class, $model->getProperty('manager')->getSchema()->getClass());

        //groups
        self::assertEquals(ModelPropertySchema::TYPE_ARRAY, $model->getProperty('groups')->getType());
        self::assertEquals(Group::class, $model->getProperty('groups')->getItems()->getClass());

        //tags
        self::assertEquals(ModelPropertySchema::TYPE_ARRAY, $model->getProperty('tags')->getType());
        self::assertEquals(ModelPropertySchema::TYPE_STRING, $model->getProperty('tags')->getItems()->getType());

        //settings
        self::assertEquals(ModelPropertySchema::TYPE_ARRAY, $model->getProperty('settings')->getType());
        self::assertEquals(ModelPropertySchema::TYPE_STRING, $model->getProperty('settings')->getAdditionalProperties()->getType());

        //parents
        self::assertEquals(ModelPropertySchema::TYPE_ARRAY, $model->getProperty('parents')->getType());
        self::assertEquals('object', $model->getProperty('parents')->getAdditionalProperties()->getType());
    }

    public function testDecoratorWithGroups()
    {
        $decorator = (new ModelSpec(User::class, ['public'], 2))->getDecorator();
        $schemaAware = new class implements SchemaAwareInterface
        {
            use SchemaAwareTrait;
        };
        $decorator($schemaAware);

        $model = $schemaAware->getSchema();
        self::assertCount(2, $model->getProperties());

        //username
        self::assertNull($model->getProperty('username'));

        //firstName & LastName
        self::assertEquals(ModelPropertySchema::TYPE_STRING, $model->getProperty('first_name')->getType());
        self::assertEquals(ModelPropertySchema::TYPE_STRING, $model->getProperty('last_name')->getType());
    }
}
