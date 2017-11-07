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

namespace Ynlo\RestfulPlatformBundle\Swagger;

use JMS\Serializer\Expression\ExpressionEvaluator;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerBuilder;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Ynlo\RestfulPlatformBundle\Swagger\Model\SwaggerObject;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Common\SpecDecorator;

class SwaggerGenerator
{
    /**
     * @param array  $specs  array of specifications
     * @param string $format desired output format, json or yaml
     *
     * @return string
     */
    public function generate(array $specs, $format = 'json')
    {
        $swaggerObject = $this->specsToObject($specs);

        $serializer = SerializerBuilder::create()
                                       ->setExpressionEvaluator(new ExpressionEvaluator(new ExpressionLanguage()))
                                       ->build();

        $context = SerializationContext::create()->setSerializeNull(false);

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