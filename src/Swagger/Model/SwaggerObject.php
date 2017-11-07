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

class SwaggerObject implements SwaggerSpecModel
{
    /**
     * @var string
     */
    protected $swagger = '2.0';

    /**
     * @var Info
     */
    protected $info;

    /**
     * @var string
     */
    protected $host;

    /**
     * @var string
     * @Serializer\SerializedName("basePath")
     */
    protected $basePath;

    /**
     * @var array|string[]
     * @Serializer\Type("array<string>")
     */
    protected $schemes = [];

    /**
     * @var array|string[]
     * @Serializer\Type("array<string>")
     */
    protected $consumes = ['application/json'];

    /**
     * @var array|string[]
     * @Serializer\Type("array<string>")
     */
    protected $produces = ['application/json'];

    /**
     * @var ArrayCollection
     */
    protected $paths;

    /**
     * @var array|Tag[]
     * @Serializer\Exclude(if="!object.getTags()")
     */
    protected $tags = [];

    /**
     * @var ArrayCollection|Schema[]
     * @Serializer\Exclude(if="object.getDefinitions().isEmpty()")
     */
    protected $definitions;

    /**
     * SwaggerObject constructor.
     */
    public function __construct()
    {
        $this->paths = new ArrayCollection();
        $this->definitions = new ArrayCollection();
        $this->info = new Info();
    }

    /**
     * @return string
     */
    public function getSwagger()
    {
        return $this->swagger;
    }

    /**
     * @param string $swagger
     */
    public function setSwagger(string $swagger)
    {
        $this->swagger = $swagger;
    }

    /**
     * @return Info
     */
    public function getInfo(): Info
    {
        return $this->info;
    }

    /**
     * @param Info $info
     */
    public function setInfo(Info $info)
    {
        $this->info = $info;
    }

    /**
     * @param string $path
     *
     * @return Path
     */
    public function getPath($path)
    {
        return $this->getPaths()->get($path);
    }

    /**
     * @return ArrayCollection|Path[]
     */
    public function getPaths(): ArrayCollection
    {
        return $this->paths;
    }

    /**
     * @param ArrayCollection|Path[] $paths
     */
    public function setPaths(ArrayCollection $paths)
    {
        $this->paths = $paths;
    }

    /**
     * @return string
     */
    public function getHost()
    {
        return $this->host;
    }

    /**
     * @param string $host
     */
    public function setHost($host)
    {
        $this->host = $host;
    }

    /**
     * @return string
     */
    public function getBasePath()
    {
        return $this->basePath;
    }

    /**
     * @param string $basePath
     */
    public function setBasePath(string $basePath)
    {
        $this->basePath = $basePath;
    }

    /**
     * @return array|\string[]
     */
    public function getSchemes()
    {
        return $this->schemes;
    }

    /**
     * @param array|\string[] $schemes
     */
    public function setSchemes($schemes)
    {
        $this->schemes = $schemes;
    }

    /**
     * @return array|\string[]
     */
    public function getConsumes()
    {
        return $this->consumes;
    }

    /**
     * @param array|\string[] $consumes
     */
    public function setConsumes($consumes)
    {
        $this->consumes = $consumes;
    }

    /**
     * @return array|\string[]
     */
    public function getProduces()
    {
        return $this->produces;
    }

    /**
     * @param array|\string[] $produces
     */
    public function setProduces($produces)
    {
        $this->produces = $produces;
    }

    /**
     * @return ArrayCollection|Schema[]
     */
    public function getDefinitions()
    {
        return $this->definitions;
    }

    /**
     * @param ArrayCollection|Schema[] $definitions
     */
    public function setDefinitions($definitions)
    {
        $this->definitions = $definitions;
    }

    /**
     * @return array|\string[]
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * @param $name
     * @param $description
     */
    public function addTag($name, $description = null)
    {
        $tag = new Tag($name, $description);
        if (!isset($this->tags[$name])) {
            $this->tags[$name] = $tag;
        }
    }
}