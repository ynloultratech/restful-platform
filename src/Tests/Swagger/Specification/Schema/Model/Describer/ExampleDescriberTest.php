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

use PHPUnit\Framework\TestCase;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema\Model\DescribeContext;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema\Model\Describer\ExampleDescriber;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema\Model\ModelPropertySchema;

class ExampleDescriberTest extends TestCase
{
    public function testDescriber()
    {
        $propertySchema = new ModelPropertySchema('name');
        $context = self::createMock(DescribeContext::class);

        $propertySchema->setType(ModelPropertySchema::TYPE_NUMBER);
        $propertySchema->setExample(null);
        (new ExampleDescriber())->describe($propertySchema, $context);
        self::assertEquals(0, $propertySchema->getExample());

        $propertySchema->setType(ModelPropertySchema::TYPE_INTEGER);
        $propertySchema->setExample(null);
        (new ExampleDescriber())->describe($propertySchema, $context);
        self::assertEquals(0, $propertySchema->getExample());

        $propertySchema->setType(ModelPropertySchema::TYPE_STRING);
        $propertySchema->setFormat(ModelPropertySchema::FORMAT_DATETIME);
        $propertySchema->setExample(null);
        (new ExampleDescriber())->describe($propertySchema, $context);
        self::assertEquals(
            (new \DateTime())->modify("00:00:00")
                             ->format('Y-m-d\TH:i:sO'),
            $propertySchema->getExample()
        );

        $propertySchema->setType(ModelPropertySchema::TYPE_STRING);
        $propertySchema->setFormat(ModelPropertySchema::FORMAT_DATE);
        $propertySchema->setExample(null);
        (new ExampleDescriber())->describe($propertySchema, $context);
        self::assertEquals(
            (new \DateTime())->modify("00:00:00")
                             ->format('Y-m-d'),
            $propertySchema->getExample()
        );

        $propertySchema->setType(ModelPropertySchema::TYPE_STRING);
        $propertySchema->setFormat(ModelPropertySchema::FORMAT_DOUBLE);
        $propertySchema->setExample(null);
        (new ExampleDescriber())->describe($propertySchema, $context);
        self::assertEquals(0.00, $propertySchema->getExample());

        $propertySchema->setType(ModelPropertySchema::TYPE_STRING);
        $propertySchema->setFormat(ModelPropertySchema::FORMAT_FLOAT);
        $propertySchema->setExample(null);
        (new ExampleDescriber())->describe($propertySchema, $context);
        self::assertEquals(0.00, $propertySchema->getExample());
    }

    public function testSupports()
    {
        $schema = self::createMock(ModelPropertySchema::class);
        $context = self::createMock(DescribeContext::class);
        self::assertTrue((new ExampleDescriber())->supports($schema, $context));
    }
}
