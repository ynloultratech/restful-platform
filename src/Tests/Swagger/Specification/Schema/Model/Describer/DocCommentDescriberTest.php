<?php
/**
 * LICENSE: This file is subject to the terms and conditions defined in
 * file 'LICENSE', which is part of this source code package.
 *
 * @copyright 2017 Copyright(c) - All rights reserved.
 * @author    YNLO-Ultratech Development Team <developer@ynloultratech.com>
 * @package   restful-platform
 */

namespace Ynlo\RestfulPlatformBundle\Tests\Swagger\Specification\Schema\Model\Describer;

use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Metadata\VirtualPropertyMetadata;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema\Model\DescribeContext;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema\Model\Describer\DocCommentDescriber;
use PHPUnit\Framework\TestCase;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema\Model\ModelPropertySchema;

class DocCommentDescriberTest extends TestCase
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
             * @var ArrayCollection
             */
            protected $arrayCollection;

            /**
             * @var string
             */
            protected $string;

            /**
             * @var bool
             */
            protected $bool;

            /**
             * @var boolean
             */
            protected $boolean;

            /**
             * @var int
             */
            protected $int;

            /**
             * @var integer
             */
            protected $integer;

            /**
             * @var float
             */
            protected $float;

            /**
             * @var double
             */
            protected $double;

            /**
             * @var \DateTime
             */
            protected $dateTime;
        };

        //string
        $propertySchema = new ModelPropertySchema($property);
        $ref = new \ReflectionProperty($class, $property);
        $context = new DescribeContext($ref);
        (new DocCommentDescriber())->describe($propertySchema, $context);
        self::assertEquals($type, $propertySchema->getType());
        self::assertEquals($format, $propertySchema->getFormat());
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
        self::assertTrue((new DocCommentDescriber())->supports($propertySchema, $context));


        $context = new DescribeContext(new VirtualPropertyMetadata($class, 'string'));
        self::assertFalse((new DocCommentDescriber())->supports($propertySchema, $context));
    }

    public function getTypes()
    {
        return [
            'string' => ['string', ModelPropertySchema::TYPE_STRING],
            'arrayCollection' => ['arrayCollection', ModelPropertySchema::TYPE_ARRAY],
            'bool' => ['bool', ModelPropertySchema::TYPE_BOOLEAN],
            'boolean' => ['boolean', ModelPropertySchema::TYPE_BOOLEAN],
            'int' => ['int', ModelPropertySchema::TYPE_NUMBER],
            'integer' => ['integer', ModelPropertySchema::TYPE_NUMBER],
            'float' => ['float', ModelPropertySchema::TYPE_NUMBER, ModelPropertySchema::FORMAT_FLOAT],
            'double' => ['double', ModelPropertySchema::TYPE_NUMBER, ModelPropertySchema::FORMAT_DOUBLE],
            '\DateTime' => ['dateTime', ModelPropertySchema::TYPE_STRING, ModelPropertySchema::FORMAT_DATETIME],
        ];
    }
}
