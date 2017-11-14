<?php
/**
 * LICENSE: This file is subject to the terms and conditions defined in
 * file 'LICENSE', which is part of this source code package.
 *
 * @copyright 2017 Copyright(c) - All rights reserved.
 * @author    YNLO-Ultratech Development Team <developer@ynloultratech.com>
 * @package   restful-platform
 */

namespace Ynlo\RestfulPlatformBundle\Swagger\Specification;

use Ynlo\RestfulPlatformBundle\Swagger\Model\Operation;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Common\CallableSpec;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema\DescriptionSpec;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Common\SpecDecorator;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Operation\ParameterSpec;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Operation\ResponseSpec;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Operation\TagSpec;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema\ModelSpec;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema\SchemaSpec;

class SWOperation
{
    /**
     * @param string $tag
     *
     * @return SpecDecorator
     */
    public static function tag(string $tag)
    {
        return new TagSpec($tag);
    }

    /**
     * @param string $description
     *
     * @return SpecDecorator
     */
    public static function description(string $description)
    {
        return new DescriptionSpec($description);
    }

    /**
     * @param $name
     * @param $specs
     *
     * @return SpecDecorator
     */
    public static function parameter($name, $specs)
    {
        return new ParameterSpec($name, $specs);
    }

    /**
     * @param        $name
     * @param string $type
     * @param array  $specs
     *
     * @return ParameterSpec
     */
    public static function parameterInQuery($name, $type = 'string', $specs = [])
    {
        return new ParameterSpec($name, array_merge([SWParameter::inQuery($type)], $specs));
    }

    /**
     * @param        $name
     * @param string $type
     * @param array  $specs
     *
     * @return ParameterSpec
     */
    public static function parameterInForm($name, $type = 'string', $specs = [])
    {
        return new ParameterSpec($name, array_merge([SWParameter::inForm($type)], $specs));
    }

    /**
     * @param        $name
     * @param string $type
     * @param array  $specs
     *
     * @return ParameterSpec
     */
    public static function parameterInPath($name, $type = 'string', $specs = [])
    {
        return new ParameterSpec($name, array_merge([SWParameter::inPath($type)], $specs));
    }

    /**
     * @param $code
     * @param $specs
     *
     * @return ResponseSpec
     */
    public static function response($code, $specs = [])
    {
        return new ResponseSpec($code, $specs);
    }

    /**
     * Body in the operation
     *
     * @param array|SchemaSpec $schema
     *
     * @return SpecDecorator
     */
    public static function body($schema)
    {
        return SWOperation::parameter(
            'body',
            [
                SWParameter::inBody($schema),
            ]
        );
    }

    /**
     * Add model as body parameter using class and groups name
     *
     * Usage:
     *
     * //Add given model as body parameter
     * SWOperation::model(User::class)
     *
     * //Use specific model groups
     * SWOperation::model(User::class, ['create'])
     *
     * //Add some extra information
     * SWOperation::model(User::class, ['create'], [SWParameter::description('User to create')])
     *
     * @param string $model
     * @param array  $groups
     * @param array  $specs
     *
     * @return SpecDecorator
     */
    public static function model($model, array $groups = [], $specs = [])
    {
        return self::body([new ModelSpec($model, $groups), $specs]);
    }

    /**
     * @param string $operationId
     *
     * @return CallableSpec
     */
    public static function operationId($operationId)
    {
        return new CallableSpec(
            function (Operation $operation) use ($operationId) {
                $operation->setOperationId($operationId);
            }
        );
    }
}