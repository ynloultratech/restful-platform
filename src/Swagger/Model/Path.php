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

use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation as Serializer;

/**
 * C@Serializer\VirtualProperty()
 */
class Path implements SwaggerSpecModel
{
    const HEAD = 'head';
    const GET = 'get';
    const POST = 'post';
    const PUT = 'put';
    const PATCH = 'patch';
    const DELETE = 'delete';
    const OPTIONS = 'options';

    /**
     * @var ArrayCollection|Operation[]
     * @Serializer\Inline()
     */
    protected $operations = [];

    /**
     * @var string
     * @Serializer\Exclude()
     */
    protected $path;

    /**
     * @param $path
     */
    public function __construct($path)
    {
        $this->path = $path;
        $this->operations = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @return ArrayCollection|Operation[]
     */
    public function getOperations(): ArrayCollection
    {
        return $this->operations;
    }

    /**
     * @param ArrayCollection|Operation[] $operations
     */
    public function setOperations(ArrayCollection $operations)
    {
        $this->operations = $operations;
    }
}