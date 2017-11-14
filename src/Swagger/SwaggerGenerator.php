<?php
/**
 * LICENSE: This file is subject to the terms and conditions defined in
 * file 'LICENSE', which is part of this source code package.
 *
 * @copyright 2017 Copyright(c) - All rights reserved.
 * @author    YNLO-Ultratech Development Team <developer@ynloultratech.com>
 * @package   restful-platform
 */

namespace Ynlo\RestfulPlatformBundle\Swagger;

use JMS\Serializer\Accessor\DefaultAccessorStrategy;
use JMS\Serializer\Accessor\ExpressionAccessorStrategy;
use JMS\Serializer\Expression\ExpressionEvaluator;
use JMS\Serializer\JsonSerializationVisitor;
use JMS\Serializer\Naming\CamelCaseNamingStrategy;
use JMS\Serializer\Naming\SerializedNameAnnotationStrategy;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerBuilder;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Ynlo\RestfulPlatformBundle\Swagger\Model\SwaggerObject;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Common\SpecDecorator;

class SwaggerGenerator
{
    /**
     * @param array  $specs   array of specifications
     * @param string $format  desired output format, json or yaml
     * @param mixed  $options options to pass to the serializer visitor, e.i. JSON_PRETTY_PRINT
     *
     * @return string
     */
    public function generate(array $specs, $format = 'json', $options = null)
    {
        $swaggerObject = $this->specsToObject($specs);

        $builder = SerializerBuilder::create();
        $expEvaluator = new ExpressionEvaluator(new ExpressionLanguage());
        $builder->setExpressionEvaluator(new ExpressionEvaluator(new ExpressionLanguage()));

        if ($format === 'json') {
            $namingStrategy = new SerializedNameAnnotationStrategy(new CamelCaseNamingStrategy());
            $visitor = new JsonSerializationVisitor($namingStrategy, new ExpressionAccessorStrategy($expEvaluator, new DefaultAccessorStrategy()));
            $visitor->setOptions($options);
            $builder->setSerializationVisitor($format, $visitor);
        }

        $serializer = $builder->build();

        $context = SerializationContext::create();
        $context->setSerializeNull(false);

        return $serializer->serialize($swaggerObject, $format, $context);


    }

    /**
     * @param array $specs
     *
     * @return SwaggerObject
     */
    protected function specsToObject(array $specs)
    {
        $swaggerObject = new SwaggerObject();
        foreach ($specs as $spec) {
            if ($spec instanceof SpecDecorator) {
                $decorator = $spec->getDecorator();
                $decorator($swaggerObject);
            }
        }

        return $swaggerObject;
    }
}