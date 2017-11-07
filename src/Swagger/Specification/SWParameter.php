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

use Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema\DescriptionSpec;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Parameter\InSpec;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Parameter\RequiredSpec;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema\SchemaSpec;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema\TypeSpec;
use Ynlo\RestfulPlatformBundle\Swagger\Model\Parameter;

class SWParameter
{
    /**
     * @param array|SchemaSpec $schema
     *
     * @return array
     */
    public static function inBody($schema)
    {
        return [
            new InSpec(Parameter::IN_BODY),
            $schema,
            self::required(),
        ];
    }

    /**
     * @param string       $type
     * @param string |null $format
     *
     * @return array
     */
    public static function inPath($type, $format = null)
    {
        return [
            new InSpec(Parameter::IN_PATH),
            new TypeSpec([$type, $format]),
            self::required(),
        ];
    }

    /**
     * @param string       $type
     * @param string |null $format
     *
     * @return array
     */
    public static function inForm($type, $format = null)
    {
        return [
            new InSpec(Parameter::IN_FORM),
            new TypeSpec([$type, $format]),
        ];
    }

    /**
     * @param string       $type
     * @param string |null $format
     *
     * @return array
     */
    public static function inQuery($type, $format = null)
    {
        return [
            new InSpec(Parameter::IN_QUERY),
            new TypeSpec([$type, $format]),
        ];
    }

    /**
     * @param string       $type
     * @param string |null $format
     *
     * @return array
     */
    public static function inHeader($type, $format = null)
    {
        return [
            new InSpec(Parameter::IN_HEADER),
            new TypeSpec([$type, $format]),
        ];
    }

    /**
     * @param string $type
     * @param null   $format
     *
     * @return array
     */
    public static function type($type, $format = null)
    {
        return [
            new TypeSpec([$type, $format]),
        ];
    }

    /**
     * @param boolean $required
     *
     * @return RequiredSpec
     */
    public static function required($required = true)
    {
        return new RequiredSpec($required);
    }

    /**
     * @param string $description
     *
     * @return DescriptionSpec
     */
    public static function description(string $description)
    {
        return new DescriptionSpec($description);
    }
}