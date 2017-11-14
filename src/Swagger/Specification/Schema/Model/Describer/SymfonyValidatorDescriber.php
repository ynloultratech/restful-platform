<?php
/**
 * LICENSE: This file is subject to the terms and conditions defined in
 * file 'LICENSE', which is part of this source code package.
 *
 * @copyright 2017 Copyright(c) - All rights reserved.
 * @author    YNLO-Ultratech Development Team <developer@ynloultratech.com>
 * @package   restful-platform
 */

namespace Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema\Model\Describer;

use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema\Model\DescribeContext;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema\Model\ModelDescriberInterface;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema\Model\ModelPropertySchema;
use Ynlo\RestfulPlatformBundle\Util\AnnotationReader;

class SymfonyValidatorDescriber implements ModelDescriberInterface
{
    /**
     * @inheritDoc
     */
    public function describe(ModelPropertySchema $property, DescribeContext $context)
    {
        if (AnnotationReader::getAnnotationFor($context->getProperty(), NotBlank::class)
            || AnnotationReader::getAnnotationFor($context->getProperty(), NotNull::class)
        ) {
            $property->setRequired(true);
        }
    }

    /**
     * @inheritDoc
     */
    public function supports(ModelPropertySchema $property, DescribeContext $context)
    {
        return ($context->getProperty() instanceof \ReflectionProperty);
    }
}