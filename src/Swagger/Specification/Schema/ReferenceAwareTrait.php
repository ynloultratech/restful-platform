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

namespace Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema;

use JMS\Serializer\Annotation as Serializer;

trait ReferenceAwareTrait
{
    /**
     * @Serializer\SerializedName("$ref")
     * @var string
     */
    protected $ref;

    /**
     * @return mixed
     */
    public function getRef()
    {
        return $this->ref;
    }

    /**
     * @param mixed $ref
     *
     * @return $this
     */
    public function setRef($ref)
    {
        $this->ref = $ref;

        return $this;
    }
}