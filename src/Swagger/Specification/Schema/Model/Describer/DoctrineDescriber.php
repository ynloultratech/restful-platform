<?php
/*
 * LICENSE: This file is subject to the terms and conditions defined in
 * file 'LICENSE', which is part of this source code package.
 *
 * @copyright 2017 Copyright(c) - All rights reserved.
 *
 * @author YNLO-Ultratech Development Team <developer@ynloultratech.com>
 * @package restful-platform
 */

namespace Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema\Model\Describer;

use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\GeneratedValue;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema\Model\DescribeContext;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema\Model\ModelDescriberInterface;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema\Model\ModelPropertySchema;
use Ynlo\RestfulPlatformBundle\Util\AnnotationReader;

class DoctrineDescriber implements ModelDescriberInterface
{
    /**
     * @inheritDoc
     */
    public function describe(ModelPropertySchema $property, DescribeContext $context)
    {
        if (!$property->getType()) {

            $type = null;

            //support for simple fields
            //NOTE: the support of associations is not added to force the use of `@Serializer\Type` annotation
            /** @var Column $column */
            if ($column = AnnotationReader::getAnnotationFor($context->getProperty(), Column::class)) {
                $type = $column->type;
            }

            $format = null;
            //common types to SwaggerTypes
            switch ($type) {
                case Type::TARRAY:
                case Type::SIMPLE_ARRAY:
                case Type::JSON_ARRAY:
                    $type = ModelPropertySchema::TYPE_ARRAY;
                    break;
                case Type::BOOLEAN:
                    $type = ModelPropertySchema::TYPE_BOOLEAN;
                    break;
                case Type::DATE:
                    $type = ModelPropertySchema::TYPE_STRING;
                    $format = ModelPropertySchema::FORMAT_DATE;
                    break;
                case Type::DATETIME:
                case Type::DATETIMETZ:
                    $type = ModelPropertySchema::TYPE_STRING;
                    $format = ModelPropertySchema::FORMAT_DATETIME;
                    break;
                case Type::DECIMAL:
                case Type::FLOAT:
                    $type = ModelPropertySchema::TYPE_NUMBER;
                    $format = ModelPropertySchema::FORMAT_FLOAT;
                    break;
                case Type::INTEGER:
                case Type::SMALLINT:
                    $type = ModelPropertySchema::TYPE_NUMBER;
                    $format = ModelPropertySchema::FORMAT_INT32;
                    break;
                case Type::BIGINT:
                    $type = ModelPropertySchema::TYPE_NUMBER;
                    $format = ModelPropertySchema::FORMAT_INT64;
                    break;
                case Type::STRING:
                case Type::TEXT:
                    $type = ModelPropertySchema::TYPE_STRING;
                    break;
                case Type::BINARY:
                    $type = ModelPropertySchema::TYPE_STRING;
                    $format = ModelPropertySchema::FORMAT_BINARY;
                    break;
            }

            $property->setType($type);
            if (!$property->getFormat() && $format) {
                $property->setFormat($format);
            }
        }

        if (AnnotationReader::getAnnotationFor($context->getProperty(), GeneratedValue::class)) {
            $property->setReadOnly(true);
        }

    }

    /**
     * @inheritDoc
     */
    public function supports(ModelPropertySchema $property, DescribeContext $context)
    {
        return $context->getProperty() instanceof \Reflector;
    }
}