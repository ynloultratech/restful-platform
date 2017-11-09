<?php

namespace Tests\Swagger\Specification\Schema\Model\Describer;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Metadata\VirtualPropertyMetadata;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema\Model\DescribeContext;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema\Model\Describer\DoctrineDescriber;
use PHPUnit\Framework\TestCase;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema\Model\ModelPropertySchema;

class DoctrineDescriberTest extends TestCase
{
    /**
     * @param string $property
     * @param string $type
     * @param string $format
     *
     * @dataProvider getTypes
     */
    public function testDescribe($property, $type, $format = null)
    {
        $class = new class
        {
            /**
             * @ORM\Column(type="array")
             */
            public $array;

            /**
             * @ORM\Column(type="simple_array")
             */
            public $simpleArray;

            /**
             * @ORM\Column(type="json_array")
             */
            public $jsonArray;

            /**
             * @ORM\Column(type="boolean")
             */
            public $boolean;

            /**
             * @ORM\Column(type="date")
             */
            public $date;

            /**
             * @ORM\Column(type="datetime")
             */
            public $datetime;

            /**
             * @ORM\Column(type="decimal")
             */
            public $decimal;

            /**
             * @ORM\Column(type="float")
             */
            public $float;

            /**
             * @ORM\Column(type="integer")
             */
            public $integer;

            /**
             * @ORM\Column(type="smallint")
             */
            public $smallint;

            /**
             * @ORM\Column(type="bigint")
             */
            public $bigint;

            /**
             * @ORM\Column(type="string")
             */
            public $string;

            /**
             * @ORM\Column(type="text")
             */
            public $text;

            /**
             * @ORM\Column(type="binary")
             */
            public $binary;
        };

        //string
        $propertySchema = new ModelPropertySchema($property);
        $ref = new \ReflectionProperty($class, $property);
        $context = new DescribeContext($ref);
        (new DoctrineDescriber())->describe($propertySchema, $context);
        self::assertEquals($type, $propertySchema->getType());
        self::assertEquals($format, $propertySchema->getFormat());
    }

    public function testDescribeReadOnly()
    {
        $class = new class
        {
            /**
             * @ORM\GeneratedValue()
             */
            public $id;
        };

        //string
        $propertySchema = new ModelPropertySchema('id');
        $ref = new \ReflectionProperty($class, 'id');
        $context = new DescribeContext($ref);
        (new DoctrineDescriber())->describe($propertySchema, $context);
        self::assertTrue($propertySchema->isReadOnly());
    }

    public function testSupports()
    {
        $class = new class
        {
            protected $string;
        };
        $propertySchema = new ModelPropertySchema('string');
        $ref = new \ReflectionProperty($class, 'string');
        $context = new DescribeContext($ref);
        self::assertTrue((new DoctrineDescriber())->supports($propertySchema, $context));


        $context = new DescribeContext(new VirtualPropertyMetadata($class, 'string'));
        self::assertFalse((new DoctrineDescriber())->supports($propertySchema, $context));
    }

    public function getTypes()
    {
        return [
            'array' => ['array', ModelPropertySchema::TYPE_ARRAY],
            'simple_array' => ['simpleArray', ModelPropertySchema::TYPE_ARRAY],
            'json_array' => ['jsonArray', ModelPropertySchema::TYPE_ARRAY],
            'boolean' => ['boolean', ModelPropertySchema::TYPE_BOOLEAN],
            'date' => ['date', ModelPropertySchema::TYPE_STRING, ModelPropertySchema::FORMAT_DATE],
            'datetime' => ['datetime', ModelPropertySchema::TYPE_STRING, ModelPropertySchema::FORMAT_DATETIME],
            'decimal' => ['decimal', ModelPropertySchema::TYPE_NUMBER, ModelPropertySchema::FORMAT_FLOAT],
            'float' => ['decimal', ModelPropertySchema::TYPE_NUMBER, ModelPropertySchema::FORMAT_FLOAT],
            'integer' => ['integer', ModelPropertySchema::TYPE_NUMBER, ModelPropertySchema::FORMAT_INT32],
            'smallint' => ['smallint', ModelPropertySchema::TYPE_NUMBER, ModelPropertySchema::FORMAT_INT32],
            'bigint' => ['bigint', ModelPropertySchema::TYPE_NUMBER, ModelPropertySchema::FORMAT_INT64],
            'string' => ['string', ModelPropertySchema::TYPE_STRING],
            'text' => ['text', ModelPropertySchema::TYPE_STRING],
            'binary' => ['binary', ModelPropertySchema::TYPE_STRING, ModelPropertySchema::FORMAT_BINARY],
        ];
    }
}
