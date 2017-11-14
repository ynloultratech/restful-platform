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

use Ynlo\RestfulPlatformBundle\Swagger\Specification\Common\CallableSpec;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema\DescriptionSpec;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema\ModelSpec;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema\SchemaSpec;
use Ynlo\RestfulPlatformBundle\Swagger\Model\Response;

class SWResponse
{
    /**
     * @param string|array $name
     * @param array        $specs
     *
     * @return SchemaSpec
     */
    public static function schema($name, array $specs = [])
    {
        return SWSchema::schema($name, $specs);
    }

    /**
     * @param string $model
     * @param array  $groups
     *
     * @return ModelSpec
     */
    public static function model($model, array $groups = [])
    {
        return SWSchema::model($model, $groups);
    }

    /**
     * @return CallableSpec
     */
    public static function emptyResponse()
    {
        return new CallableSpec(
            function (Response $spec) {
                $spec->setSchema(null);
            }
        );
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