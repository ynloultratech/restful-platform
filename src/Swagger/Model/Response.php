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

namespace Ynlo\RestfulPlatformBundle\Swagger\Model;

use JMS\Serializer\Annotation as Serializer;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema\DescriptionAwareInterface;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema\DescriptionAwareTrait;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema\SchemaAwareInterface;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema\SchemaAwareTrait;

class Response implements
    SwaggerSpecModel,
    DescriptionAwareInterface,
    SchemaAwareInterface
{
    use DescriptionAwareTrait;
    use SchemaAwareTrait;

    /**
     * @var string
     * @Serializer\Exclude()
     */
    protected $code;

    /**
     * Response constructor.
     *
     * @param string|integer $code
     */
    public function __construct($code)
    {
        $this->code = $code;
    }

    /**
     * @return string|integer
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param string|integer $code
     *
     * @return $this;
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }
}