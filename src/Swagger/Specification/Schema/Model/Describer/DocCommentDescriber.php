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

use Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema\Model\DescribeContext;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema\Model\ModelDescriberInterface;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema\Model\ModelPropertySchema;

class DocCommentDescriber implements ModelDescriberInterface
{
    /**
     * @inheritDoc
     */
    public function describe(ModelPropertySchema $property, DescribeContext $context)
    {
        if (!$property->getType()) {
            $comment = $context->getProperty()->getDocComment();
            if (preg_match('/@(var|return)\s+([^\n\s]+)/', $comment, $matches)) {
                if (isset($matches[2])) {
                    $type = $matches[2];
                    $format = null;

                    //common types to SwaggerTypes
                    switch ($type) {
                        case 'Collection':
                            $type = ModelPropertySchema::TYPE_ARRAY;
                            break;
                        case 'bool':
                            $type = ModelPropertySchema::TYPE_BOOLEAN;
                            break;
                        case 'string':
                            $type = ModelPropertySchema::TYPE_STRING;
                            break;
                        case 'int':
                        case 'integer':
                            $type = ModelPropertySchema::TYPE_NUMBER;
                            break;
                        case 'float':
                            $type = ModelPropertySchema::TYPE_NUMBER;
                            $format = ModelPropertySchema::FORMAT_FLOAT;
                            break;
                        case 'duble':
                            $type = ModelPropertySchema::TYPE_NUMBER;
                            $format = ModelPropertySchema::FORMAT_DOUBLE;
                            break;
                        case '\DateTime':
                            $type = ModelPropertySchema::TYPE_STRING;
                            $format = ModelPropertySchema::FORMAT_DATETIME;
                            break;
                    }

                    $property->setType($type);

                    if ($format && $property->getFormat()) {
                        $property->setFormat($format);
                    }
                }
            }
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