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

class SwaggerObject implements SwaggerSpecModel
{
    const SWAGGER_v2 = '2.0';
    const SWAGGER_v3 = '3.0';//TODO: implement the new specification

    /**
     * @var string
     */
    protected $swagger = self::SWAGGER_v2;

    /**
     * @var Info
     */
    protected $info;

    /**
     * @var string
     */
    protected $host = '';

    /**
     * @var string
     * @Serializer\SerializedName("basePath")
     */
    protected $basePath = '';

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
     * @Serializer\Type("array<Ynlo\RestfulPlatformBundle\Swagger\Model\Tag>")
     */
    protected $tags = [];

    /**
     * @var ArrayCollection
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
    public function getSwagger(): string
    {
        return $this->swagger;
    }

    /**
     * @param string $swagger
     *
     * @return $this
     */
    public function setSwagger(string $swagger)
    {
        $this->swagger = $swagger;

        return $this;
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
     *
     * @return $this
     */
    public function setInfo(Info $info)
    {
        $this->info = $info;

        return $this;
    }

    /**
     * @param string $path
     *
     * @return Path|null
     */
    public function getPath($path)
    {
        return $this->getPaths()->get($path);
    }

    /**
     * @return ArrayCollection
     */
    public function getPaths(): ArrayCollection
    {
        return $this->paths;
    }

    /**
     * @param ArrayCollection $paths
     *
     * @return $this
     */
    public function setPaths(ArrayCollection $paths)
    {
        $this->paths = $paths;

        return $this;
    }

    /**
     * @return string
     */
    public function getHost(): string
    {
        return $this->host;
    }

    /**
     * @param string $host
     *
     * @return $this
     */
    public function setHost(string $host)
    {
        $this->host = $host;

        return $this;
    }

    /**
     * @return string
     */
    public function getBasePath(): string
    {
        return $this->basePath;
    }

    /**
     * @param string $basePath
     *
     * @return $this
     */
    public function setBasePath(string $basePath)
    {
        $this->basePath = $basePath;

        return $this;
    }

    /**
     * @return array|\string[]
     */
    public function getSchemes(): array
    {
        return $this->schemes;
    }

    /**
     * @param array|\string[] $schemes
     *
     * @return $this;
     */
    public function setSchemes(array $schemes)
    {
        $this->schemes = $schemes;

        return $this;
    }

    /**
     * @return array|\string[]
     */
    public function getConsumes(): array
    {
        return $this->consumes;
    }

    /**
     * @param array|\string[] $consumes
     *
     * @return $this
     */
    public function setConsumes($consumes)
    {
        $this->consumes = $consumes;

        return $this;
    }

    /**
     * @return array|\string[]
     */
    public function getProduces(): array
    {
        return $this->produces;
    }

    /**
     * @param array|\string[] $produces
     *
     * @return $this
     */
    public function setProduces($produces)
    {
        $this->produces = $produces;

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getDefinitions(): ArrayCollection
    {
        return $this->definitions;
    }

    /**
     * @param ArrayCollection $definitions
     *
     * @return $this;
     */
    public function setDefinitions($definitions)
    {
        $this->definitions = $definitions;

        return $this;
    }

    /**
     * @return array|\string[]
     */
    public function getTags(): array
    {
        return $this->tags;
    }

    /**
     * @param string $name
     * @param string $description
     *
     * @return $this
     */
    public function addTag(string $name, $description = null)
    {
        $tag = new Tag($name, $description);
        if (!isset($this->tags[$name])) {
            $this->tags[$name] = $tag;
        }

        return $this;
    }
}