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
 * Contains one single value to assign in the decorator,
 * is the base class for many decorators
 */
abstract class ValueSpec implements SpecDecorator
{
    protected $value;

    public function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * @inheritdoc
     */
    public function getDecorator(): callable
    {
        return function ($specification) {
            $this->setValue($specification, $this->value);
        };
    }

    abstract public function setValue($spec, $value);
}