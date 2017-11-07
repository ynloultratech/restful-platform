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

namespace Ynlo\RestfulPlatformBundle\Swagger\Specification;

use Ynlo\RestfulPlatformBundle\Swagger\Model\Schema;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Common\CallableSpec;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema\AdditionalPropertiesSpec;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema\DescriptionSpec;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema\EnumSpec;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema\ExampleSpec;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema\GroupsSpec;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema\ItemSpec;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema\ModelSpec;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema\PropertySpec;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema\SchemaSpec;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema\TypeSpec;

class SWSchema
{
    /**
     * @param string $class
     * @param array  $groups
     *
     * @return ModelSpec
     */
    public static function model($class, array $groups = [])
    {
        return new ModelSpec($class, $groups);
    }

    /**
     * @param array $groups
     *
     * @return GroupsSpec
     */
    public static function groups($groups = [])
    {
        return new GroupsSpec($groups);
    }

    /**
     * Map specific schema to given class.
     * Use when the schema is generated manually but is needed
     * internally map to specific class.
     *
     * its used to know the internal class of specification models
     *
     * @param $class
     *
     * @return CallableSpec
     */
    public static function mappedClass($class)
    {
        return new CallableSpec(
            function (Schema $spec) use ($class) {
                $spec->setClass($class);
            }
        );
    }

    /**
     * @param string|array $name
     * @param array        $specs
     *
     * @return SchemaSpec
     */
    public static function schema($name, array $specs = [])
    {
        if (is_array($name)) {
            $specs = $name;
            $name = null;
        }

        return new SchemaSpec($name, $specs);
    }

    /**
     * Add property to current schema
     *
     * Usage:
     *
     * //simple properties
     * SWSchema::property('name', 'string'),
     * SWSchema::property('age', 'integer'),
     *
     * //format
     * SWSchema::property('birthDate', 'string', 'date-time'),
     *
     * //additional specs
     * SWSchema::property('birthDate', 'string', 'date-time', [SWSchema::example('1985-06-18')]),
     *
     * //using another schema as type
     * SWSchema::property('user', SWSchema::schema([
     *      SWSchema::property('username', 'string'),
     *      SWSchema::property('password', 'integer'),
     * ])),
     *
     * @param string            $name   property name
     * @param string|SchemaSpec $type   property type
     * @param null              $format property format
     * @param array             $specs  extra schema specifications to apply, e.g. [SWSchema::description('property description')]
     *
     * @return PropertySpec
     */
    public static function property($name, $type, $format = null, array $specs = [])
    {
        if ($type instanceof SchemaSpec || $type instanceof ModelSpec) {
            return new PropertySpec($name, array_merge([$type], $specs));
        }

        $defaultSpecs = [
            SWSchema::type($type, $format),
        ];
        $specs = array_merge($defaultSpecs, $specs);

        return new PropertySpec($name, $specs);
    }

    /**
     * @param      $type
     * @param null $format
     *
     * @return TypeSpec
     */
    public static function type($type, $format = null)
    {
        return new TypeSpec([$type, $format]);
    }

    /**
     * @param string $description
     *
     * @return DescriptionSpec
     */
    public static function description($description)
    {
        return new DescriptionSpec($description);
    }

    /**
     * @param array $enum
     *
     * @return EnumSpec
     */
    public static function enum($enum)
    {
        return new EnumSpec($enum);
    }

    /**
     * @param mixed $example
     *
     * @return ExampleSpec
     */
    public static function example($example)
    {
        return new ExampleSpec($example);
    }

    /**
     * Set the type of items when type is 'array'
     *
     * Usage:
     *
     * //array of strings
     * SWSchema::items('string')
     *
     * Produces:
     * ['string', 'string']
     *
     * //array of specific model
     * SWSchema::items(SWSchema::model(User::class))
     *
     * Produces:
     * [ {"username":"Jhon", "password":"1234"} ]
     *
     * //array of custom schema
     * SWSchema::items(SWSchema::schema('Error',
     *                                     [
     *                                      SWSchema::property('code', 'integer'),
     *                                      SWSchema::property('msg', 'string'),
     *                                     ]
     *                  ))
     *
     * Produces:
     * [ {"code":"1", "msg":"message string"} ]
     *
     *
     *
     * @param string|SchemaSpec|ModelSpec $type
     * @param array                       $specs additional specs
     *
     * @return ItemSpec
     */
    public static function items($type, $specs = [])
    {
        return new ItemSpec($type, $specs);
    }

    /**
     * @param $type
     *
     * @return AdditionalPropertiesSpec
     */
    public static function additionalProperties($type)
    {
        return new AdditionalPropertiesSpec($type);
    }
}