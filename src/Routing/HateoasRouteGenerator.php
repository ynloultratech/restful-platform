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

use Hateoas\UrlGenerator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Router;

class HateoasRouteGenerator implements UrlGeneratorInterface
{
    /**
     * @var Router
     */
    protected $router;

    /**
     * @var RequestStack
     */
    protected $requestStack;

    /**
     * HateoasRouteGenerator constructor.
     *
     * @param Router $router
     * @param        $requestStack
     */
    public function __construct(Router $router, $requestStack)
    {
        $this->router = $router;
        $this->requestStack = $requestStack;
    }

    /**
     * @inheritDoc
     */
    public function generate($name, array $parameters, $absolute = false)
    {
        //TODO: get from config
        $version = $this->requestStack->getCurrentRequest()->get('version', 'v1');
        $parameters = array_merge(['version' => $version], $parameters);

        return $this->router->generate($name, $parameters, $absolute);
    }

}