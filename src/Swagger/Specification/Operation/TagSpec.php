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

namespace Ynlo\RestfulPlatformBundle\Swagger\Specification\Operation;

use Ynlo\RestfulPlatformBundle\Swagger\Specification\Common\SpecDecorator;
use Ynlo\RestfulPlatformBundle\Swagger\Model\Operation;

class TagSpec implements SpecDecorator
{
    protected $tag;

    /**
     * TagSpec constructor.
     *
     * @param $tag
     */
    public function __construct($tag)
    {
        $this->tag = $tag;
    }

    public function getDecorator(): callable
    {
        return function (Operation $swaggerOperation) {
            $swaggerOperation->getTags()->set($this->tag, $this->tag);
        };
    }
}