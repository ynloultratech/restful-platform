<?php
/**
 * LICENSE: This file is subject to the terms and conditions defined in
 * file 'LICENSE', which is part of this source code package.
 *
 * @copyright 2017 Copyright(c) - All rights reserved.
 * @author    YNLO-Ultratech Development Team <developer@ynloultratech.com>
 * @package   restful-platform
 */

namespace Ynlo\RestfulPlatformBundle\Api;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class RestApiPool implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @var RestApiInterface[]
     */
    protected $elements = [];

    /**
     * @param RestApiInterface $restApi
     */
    public function addApi(RestApiInterface $restApi)
    {
        $restApi->setContainer($this->container);
        $this->elements[] = $restApi;
    }

    /**
     * @param string $class class name of the API or managed entity
     *
     * @return RestApiInterface
     */
    public function getApiByClass($class)
    {
        foreach ($this->getElements() as $serviceId => $element) {
            if (get_class($element) === $class) {
                return $element;
            }

            if ($element->getResourceClass() === $class) {
                return $element;
            }
        }

        return null;
    }

    /**
     * @return RestApiInterface[]
     */
    public function getElements(): array
    {
        return $this->elements;
    }
}