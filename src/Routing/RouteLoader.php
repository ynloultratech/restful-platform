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

namespace Ynlo\RestfulPlatformBundle\Routing;

use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\Routing\RouteCollection;
use Ynlo\RestfulPlatformBundle\Api\RestApiPool;

class RouteLoader extends Loader
{
    /**
     * @var
     */
    protected $loaded = false;

    /**
     * @var RestApiPool
     */
    protected $apiPool;

    /**
     * RouteLoader constructor.
     *
     * @param RestApiPool $restApiPool
     */
    public function __construct(RestApiPool $restApiPool)
    {
        $this->apiPool = $restApiPool;
    }

    /**
     * @inheritDoc
     */
    public function load($resource, $type = null)
    {
        if (true === $this->loaded) {
            throw new \RuntimeException('Do not add the "api" loader twice');
        }

        $routes = new RouteCollection();
        foreach ($this->apiPool->getElements() as $api) {
            foreach ($api->getRoutes()->getElements() as $name => $route) {
                $routes->add($name, $route);
            }
        }

        $this->loaded = true;

        return $routes;
    }

    /**
     * @inheritDoc
     */
    public function supports($resource, $type = null)
    {
        return 'restful' === $type;
    }
}