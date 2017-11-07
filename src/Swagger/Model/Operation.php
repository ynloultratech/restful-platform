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
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema\DescriptionAwareInterface;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema\DescriptionAwareTrait;

class Operation implements
    SwaggerSpecModel,
    DescriptionAwareInterface
{
    use DescriptionAwareTrait;

    /**
     * @var string
     * @Serializer\SerializedName("operationId")
     */
    protected $operationId;

    /**
     * @var ArrayCollection|string[]
     * @Serializer\Type("array<string>")
     * @Serializer\Exclude(if="object.getTags().isEmpty()")
     */
    protected $tags;

    /**
     * @var string
     */
    protected $summary;

    /**
     * @var ArrayCollection|Parameter[]
     * @Serializer\Type("ArrayCollection<Ynlo\RestfulPlatformBundle\Swagger\Model\Parameter>")
     * @Serializer\Exclude(if="object.getParameters().isEmpty()")
     */
    protected $parameters;

    /**
     * @var ArrayCollection|Response[]
     * @Serializer\Type("ArrayCollection<integer,Ynlo\RestfulPlatformBundle\Swagger\Model\Response>")
     * @Serializer\Exclude(if="object.getResponses().isEmpty()")
     */
    protected $responses;

    /**
     * SWOperation constructor.
     */
    public function __construct()
    {
        $this->tags = new ArrayCollection();
        $this->parameters = new ArrayCollection();
        $this->responses = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function getOperationId(): string
    {
        return $this->operationId;
    }

    /**
     * @param string $operationId
     */
    public function setOperationId(string $operationId)
    {
        $this->operationId = $operationId;
    }

    /**
     * @return ArrayCollection|\string[]
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * @param ArrayCollection|\string[] $tags
     */
    public function setTags($tags)
    {
        $this->tags = $tags;
    }

    /**
     * @return string
     */
    public function getSummary(): string
    {
        return $this->summary;
    }

    /**
     * @param string $summary
     */
    public function setSummary(string $summary)
    {
        $this->summary = $summary;
    }

    /**
     * @return ArrayCollection|Parameter[]
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * @param ArrayCollection|Parameter[] $parameters
     */
    public function setParameters($parameters)
    {
        $this->parameters = $parameters;
    }

    /**
     * @return ArrayCollection|Response[]
     */
    public function getResponses()
    {
        return $this->responses;
    }

    /**
     * @param integer $code
     *
     * @return Response
     */
    public function getResponse($code)
    {
        return $this->responses->get($code);
    }

    /**
     * @param ArrayCollection|Response[] $responses
     */
    public function setResponses($responses)
    {
        $this->responses = $responses;
    }
}