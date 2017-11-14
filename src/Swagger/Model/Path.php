<?php
/**
 * LICENSE: This file is subject to the terms and conditions defined in
 * file 'LICENSE', which is part of this source code package.
 *
 * @copyright 2017 Copyright(c) - All rights reserved.
 * @author    YNLO-Ultratech Development Team <developer@ynloultratech.com>
 * @package   restful-platform
 */

namespace Ynlo\RestfulPlatformBundle\Swagger\Model;

use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation as Serializer;

/**
 * @Serializer\VirtualProperty()
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
     * @var ArrayCollection
     * @Serializer\Inline()
     */
    protected $operations = [];

    /**
     * @var string
     * @Serializer\Exclude()
     */
    protected $path;

    /**
     * @param string $path
     */
    public function __construct(string $path)
    {
        $this->path = $path;
        $this->operations = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @return ArrayCollection
     */
    public function getOperations(): ArrayCollection
    {
        return $this->operations;
    }

    /**
     * @param ArrayCollection $operations
     *
     * @return $this
     */
    public function setOperations(ArrayCollection $operations)
    {
        $this->operations = $operations;

        return $this;
    }
}