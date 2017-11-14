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

use JMS\Serializer\Metadata\VirtualPropertyMetadata;
use Ynlo\RestfulPlatformBundle\Annotation\Description;
use Ynlo\RestfulPlatformBundle\Annotation\Enum;
use Ynlo\RestfulPlatformBundle\Annotation\Example;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema\Model\DescribeContext;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema\Model\Describer\SwaggerAnnotationsDescriber;
use PHPUnit\Framework\TestCase;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema\Model\ModelPropertySchema;

class SwaggerAnnotationsDescriberTest extends TestCase
{
    public function testDescribe()
    {
        $class = new class
        {
            /**
             * @Example("example")
             * @Description("description")
             * @Enum({"pending","completed"})
             */
            protected $name;
        };

        $propertySchema = new ModelPropertySchema('name');
        $ref = new \ReflectionProperty($class, 'name');
        $context = new DescribeContext($ref);
        (new SwaggerAnnotationsDescriber())->describe($propertySchema, $context);
        self::assertEquals('example', $propertySchema->getExample());
        self::assertEquals('description', $propertySchema->getDescription());
        self::assertEquals(["pending", "completed"], $propertySchema->getEnum());
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
        self::assertTrue((new SwaggerAnnotationsDescriber())->supports($propertySchema, $context));

        $context = new DescribeContext(new VirtualPropertyMetadata($class, 'string'));
        self::assertFalse((new SwaggerAnnotationsDescriber())->supports($propertySchema, $context));
    }
}
