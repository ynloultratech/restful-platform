<?php

namespace Tests\Swagger\Specification\Schema\Model\Describer;

use JMS\Serializer\Annotation as Serializer;
use JMS\Serializer\Metadata\VirtualPropertyMetadata;
use JMS\Serializer\Naming\CamelCaseNamingStrategy;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema\Model\DescribeContext;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema\Model\Describer\JMSSerializerDescriber;
use PHPUnit\Framework\TestCase;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema\Model\ModelPropertySchema;
use Ynlo\RestfulPlatformBundle\Util\SerializerReader;

class JMSSerializerDescriberTest extends TestCase
{
    /**
     * @param string $property
     * @param string $type
     * @param string $formatOrItemType
     * @param string $keyType
     *
     * @dataProvider getTypes
     */
    public function testDescribe($property, $type, $formatOrItemType = null, $keyType = null)
    {
        $class = new class
        {
            /**
             * @Serializer\Type("string")
             */
            public $string;

            /**
             * @Serializer\Type("integer")
             */
            public $integer;

            /**
             * @Serializer\Type("int")
             */
            public $int;

            /**
             * @Serializer\Type("boolean")
             */
            public $boolean;

            /**
             * @Serializer\Type("bool")
             */
            public $bool;

            /**
             * @Serializer\Type("double")
             */
            public $double;

            /**
             * @Serializer\Type("float")
             */
            public $float;

            /**
             * @Serializer\Type("DateTime")
             */
            public $datetime;

            /**
             * @Serializer\Type("DateTime<y/m/d>")
             */
            public $datetimeFormatted;

            /**
             * @Serializer\Type("array<string>")
             */
            public $array;

            /**
             * @Serializer\Type("array<string,string>")
             */
            public $associativeArray;
        };

        //string
        $propertySchema = new ModelPropertySchema($property);
        $ref = new \ReflectionProperty($class, $property);
        $context = new DescribeContext($ref);
        (new JMSSerializerDescriber())->describe($propertySchema, $context);
        self::assertEquals($type, $propertySchema->getType());

        if ($type === ModelPropertySchema::TYPE_ARRAY) {
            self::assertEquals($formatOrItemType, $propertySchema->getItemType());
        } else {
            self::assertEquals($formatOrItemType, $propertySchema->getFormat());
        }
    }

    public function testDescribeReadOnly()
    {
        $class = new class
        {
            /**
             * @Serializer\Type("string")
             * @Serializer\ReadOnly()
             */
            public $readOnly;
        };

        //using reflection
        $propertySchema = new ModelPropertySchema(new \ReflectionProperty($class, 'readOnly'));
        $ref = new \ReflectionProperty($class, 'readOnly');
        $context = new DescribeContext($ref);
        (new JMSSerializerDescriber())->describe($propertySchema, $context);
        self::assertTrue($propertySchema->isReadOnly());

        //using virtual property
        $property = new VirtualPropertyMetadata($class, 'readOnly');
        $property->readOnly = true;
        $property->serializedName = 'readOnly';
        $context = new DescribeContext($property);
        $propertySchema = new ModelPropertySchema('readOnly');
        (new JMSSerializerDescriber())->describe($propertySchema, $context);
        self::assertTrue($propertySchema->isReadOnly());
    }

    public function testDescribeName()
    {
        $class = new class
        {
            /**
             * @Serializer\SerializedName("userName")
             */
            public $name;

            public $firstName;
        };

        //using SerializedName Annotation
        $propertySchema = new ModelPropertySchema(new \ReflectionProperty($class, 'name'));
        $ref = new \ReflectionProperty($class, 'name');
        $context = new DescribeContext($ref);
        (new JMSSerializerDescriber())->describe($propertySchema, $context);
        self::assertEquals('userName', $propertySchema->getName());

        //using the SerializerReader
        SerializerReader::$namingStrategy = new CamelCaseNamingStrategy();
        $propertySchema = new ModelPropertySchema(new \ReflectionProperty($class, 'firstName'));
        $ref = new \ReflectionProperty($class, 'firstName');
        $context = new DescribeContext($ref);
        (new JMSSerializerDescriber())->describe($propertySchema, $context);
        self::assertEquals('first_name', $propertySchema->getName());

        //using virtual property
        $property = new VirtualPropertyMetadata($class, 'virtual');
        $property->serializedName = 'virtual';
        $context = new DescribeContext($property);
        $propertySchema = new ModelPropertySchema('virtual');
        (new JMSSerializerDescriber())->describe($propertySchema, $context);
        self::assertEquals($property->serializedName, $propertySchema->getName());
    }

    public function testDescribeType()
    {
        $class = new class
        {
            /**
             * @Serializer\Type("string")
             */
            public $name;

            public $firstName;
        };

        //using SerializedName Annotation
        $propertySchema = new ModelPropertySchema(new \ReflectionProperty($class, 'name'));
        $ref = new \ReflectionProperty($class, 'name');
        $context = new DescribeContext($ref);
        (new JMSSerializerDescriber())->describe($propertySchema, $context);
        self::assertEquals('string', $propertySchema->getType());

        //using virtual property
        $property = new VirtualPropertyMetadata($class, 'virtual');
        $property->serializedName = 'virtual';
        $property->type = 'integer';
        $context = new DescribeContext($property);
        $propertySchema = new ModelPropertySchema('virtual');
        (new JMSSerializerDescriber())->describe($propertySchema, $context);
        self::assertEquals($property->type, $propertySchema->getType());
    }

    public function testDescribeGroups()
    {
        $class = new class
        {
            /**
             * @Serializer\Groups({"public"})
             */
            public $name;
        };

        //using SerializedName Annotation
        $propertySchema = new ModelPropertySchema(new \ReflectionProperty($class, 'name'));
        $ref = new \ReflectionProperty($class, 'name');
        $context = new DescribeContext($ref);
        (new JMSSerializerDescriber())->describe($propertySchema, $context);
        self::assertEquals(['public'], $propertySchema->getGroups());

        //using virtual property
        $property = new VirtualPropertyMetadata($class, 'virtual');
        $property->serializedName = 'virtual';
        $property->groups = ['public'];
        $context = new DescribeContext($property);
        $propertySchema = new ModelPropertySchema('virtual');
        (new JMSSerializerDescriber())->describe($propertySchema, $context);
        self::assertEquals($property->groups, $propertySchema->getGroups());
    }

    public function testSupports()
    {
        $schema = self::createMock(ModelPropertySchema::class);
        $context = self::createMock(DescribeContext::class);
        self::assertTrue((new JMSSerializerDescriber())->supports($schema, $context));
    }

    public function getTypes()
    {
        return [
            'string' => ['string', ModelPropertySchema::TYPE_STRING],
            'integer' => ['integer', ModelPropertySchema::TYPE_INTEGER],
            'int' => ['int', ModelPropertySchema::TYPE_INTEGER],
            'boolean' => ['boolean', ModelPropertySchema::TYPE_BOOLEAN],
            'bool' => ['bool', ModelPropertySchema::TYPE_BOOLEAN],
            'double' => ['double', ModelPropertySchema::TYPE_NUMBER, ModelPropertySchema::FORMAT_DOUBLE],
            'float' => ['float', ModelPropertySchema::TYPE_NUMBER, ModelPropertySchema::FORMAT_FLOAT],
            'datetime' => ['datetime', ModelPropertySchema::TYPE_STRING, ModelPropertySchema::FORMAT_DATETIME],
            'datetimeFormatted' => ['datetimeFormatted', ModelPropertySchema::TYPE_STRING, 'y/m/d'],
            'array' => ['array', ModelPropertySchema::TYPE_ARRAY, ModelPropertySchema::TYPE_STRING],
            'associativeArray' => [
                'associativeArray',
                ModelPropertySchema::TYPE_ARRAY,
                ModelPropertySchema::TYPE_STRING,
                ModelPropertySchema::TYPE_STRING,
            ],
        ];
    }
}
