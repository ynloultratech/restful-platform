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

use Ynlo\RestfulPlatformBundle\Swagger\Model\Path;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Path\OperationSpec;

class SWPath
{
    /**
     * @param array $specs
     *
     * @return OperationSpec
     */
    public static function get(array $specs)
    {
        return new OperationSpec(Path::GET, $specs);
    }

    /**
     * @param array $specs
     *
     * @return OperationSpec
     */
    public static function post(array $specs)
    {
        return new OperationSpec(Path::POST, $specs);
    }

    /**
     * @param array $specs
     *
     * @return OperationSpec
     */
    public static function put(array $specs)
    {
        return new OperationSpec(Path::PUT, $specs);
    }

    /**
     * @param array $specs
     *
     * @return OperationSpec
     */
    public static function patch(array $specs)
    {
        return new OperationSpec(Path::PATCH, $specs);
    }

    /**
     * @param array $specs
     *
     * @return OperationSpec
     */
    public static function delete(array $specs)
    {
        return new OperationSpec(Path::DELETE, $specs);
    }

    /**
     * @param array $specs
     *
     * @return OperationSpec
     */
    public static function head(array $specs)
    {
        return new OperationSpec(Path::HEAD, $specs);
    }

    /**
     * @param array $specs
     *
     * @return OperationSpec
     */
    public static function options(array $specs)
    {
        return new OperationSpec(Path::OPTIONS, $specs);
    }
}