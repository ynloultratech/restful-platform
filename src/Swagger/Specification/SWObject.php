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

use Ynlo\RestfulPlatformBundle\Swagger\Model\SwaggerObject;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Common\CallableSpec;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Root\InfoSpec;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Root\PathSpec;

class SWObject
{
    /**
     * @param string $title
     * @param string $description
     * @param string $version
     *
     * @return InfoSpec
     */
    public static function info($title, $description = '', $version = '')
    {
        return new InfoSpec($title, $description, $version);
    }

    /**
     * @param string $basePath
     *
     * @return CallableSpec
     */
    public static function basePath($basePath)
    {
        return new CallableSpec(
            function (SwaggerObject $swaggerObject) use ($basePath) {
                $swaggerObject->setBasePath($basePath);
            }
        );
    }

    /**
     * @param string $host
     *
     * @return CallableSpec
     */
    public static function host($host)
    {
        return new CallableSpec(
            function (SwaggerObject $swaggerObject) use ($host) {
                $swaggerObject->setHost($host);
            }
        );
    }

    /**
     * @param string $path
     * @param array  $operations
     *
     * @return PathSpec
     */
    public static function path($path, array $operations)
    {
        return new PathSpec($path, $operations);
    }
}