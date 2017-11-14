<?php
/**
 * LICENSE: This file is subject to the terms and conditions defined in
 * file 'LICENSE', which is part of this source code package.
 *
 * @copyright 2017 Copyright(c) - All rights reserved.
 * @author    YNLO-Ultratech Development Team <developer@ynloultratech.com>
 * @package   restful-platform
 */

namespace Ynlo\RestfulPlatformBundle\Swagger\Specification\Common;

/**
 * The decorator is called using a callable instead of specific class
 */
class CallableSpec implements SpecDecorator
{
    protected $callable;

    public function __construct($callable)
    {
        $this->callable = $callable;
    }

    /**
     * @inheritdoc
     */
    public function getDecorator(): callable
    {
        return function ($specification) {
            call_user_func_array($this->callable, [$specification]);
        };
    }
}