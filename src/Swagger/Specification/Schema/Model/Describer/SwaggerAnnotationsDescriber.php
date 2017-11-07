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

use Ynlo\RestfulPlatformBundle\Annotation\Description;
use Ynlo\RestfulPlatformBundle\Annotation\Enum;
use Ynlo\RestfulPlatformBundle\Annotation\Example;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema\Model\DescribeContext;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema\Model\ModelDescriberInterface;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema\Model\ModelPropertySchema;
use Ynlo\RestfulPlatformBundle\Util\AnnotationReader;

class SwaggerAnnotationsDescriber implements ModelDescriberInterface
{
    /**
     * @inheritDoc
     */
    public function describe(ModelPropertySchema $property, DescribeContext $context)
    {
        /** @var Example $example */
        if (!$property->getExample()) {
            $example = AnnotationReader::getAnnotationFor($context->getProperty(), Example::class);
            if ($example) {
                $property->setExample($example->example);
            }
        }

        /** @var Description $description */
        if (!$property->getDescription()) {
            $description = AnnotationReader::getAnnotationFor($context->getProperty(), Description::class);
            if ($description) {
                $property->setDescription($description->description);
            }
        }

        /** @var Enum $enum */
        if (!$property->getEnum()) {
            $enum = AnnotationReader::getAnnotationFor($context->getProperty(), Enum::class);
            if ($enum) {
                $property->setEnum($enum->values);
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