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

use Doctrine\Common\Util\Inflector;
use Symfony\Component\Routing\Route;
use Ynlo\RestfulPlatformBundle\Api\RestApiInterface;

class ApiRouteCollection
{
    /**
     * @var Route[]
     */
    protected $elements = [];

    /**
     * @var RestApiInterface
     */
    protected $api;

    /**
     * @param RestApiInterface $api
     */
    public function __construct(RestApiInterface $api)
    {
        $this->api = $api;
    }

    /**
     * Add route.
     *
     * @param string $method       Method
     * @param string $action       Name
     * @param string $pattern      Pattern (will be automatically combined with @see $this->baseRoutePattern and $action
     * @param array  $defaults     Defaults
     * @param array  $requirements Requirements
     * @param array  $options      Options
     * @param string $host         Host
     * @param array  $schemes      Schemes
     * @param string $condition    Condition
     *
     * @return ApiRouteCollection
     */
    public function add($method, $action, $pattern = null, array $defaults = [], array $requirements = [], array $options = [], $host = '', array $schemes = [], $condition = '')
    {
        $pattern = $this->getBaseRoutePattern().($pattern ? '/'.$pattern : null);

        $routeName = $this->getRouteName($action);

        if (!isset($defaults['_controller'])) {
            $defaults['_controller'] = $this->getBaseControllerName().':'.$this->actionify($action);
        }

        if (!isset($defaults['_api'])) {
            $apiAction = $action.'_operation';
            $defaults['_api'] = get_class($this->api).':'.Inflector::camelize($apiAction);
        }

        $this->elements[$routeName] = function () use (
            $pattern,
            $defaults,
            $requirements,
            $options,
            $host,
            $schemes,
            $method,
            $condition
        ) {
            return new Route($pattern, $defaults, $requirements, $options, $host, $schemes, [$method], $condition);
        };

        return $this;
    }

    public function getRouteName($action)
    {
        return $this->getBaseRouteName().'_'.$action;
    }

    /**
     * @return Route[]
     */
    public function getElements()
    {
        foreach ($this->elements as $name => $element) {
            $this->elements[$name] = $this->resolve($element);
        }

        return $this->elements;
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function has($name)
    {
        return array_key_exists($this->getRouteName($name), $this->elements);
    }

    /**
     * @param string $action
     *
     * @throws \InvalidArgumentException
     *
     * @return Route
     */
    public function get($action)
    {
        if ($this->has($action)) {
            $routeName = $this->getRouteName($action);

            $this->elements[$routeName] = $this->resolve($this->elements[$routeName]);

            return $this->elements[$routeName];
        }

        throw new \RuntimeException(sprintf('Route "%s" does not exist.', $action));
    }

    /**
     * @param string|string[] $action
     *
     * @return ApiRouteCollection
     */
    public function remove($action)
    {
        if (is_array($action)) {
            foreach ((array) $action as $act) {
                $this->remove($act);
            }

            return $this;
        }

        unset($this->elements[$this->getRouteName($action)]);

        return $this;
    }

    /**
     * Remove all routes except routes in $routeList.
     *
     * @param string[]|string $routeList
     *
     * @return ApiRouteCollection
     */
    public function clearExcept($routeList)
    {
        if (!is_array($routeList)) {
            $routeList = [$routeList];
        }

        $routeCodeList = [];
        foreach ($routeList as $action) {
            $routeCodeList[] = $this->getRouteName($action);
        }

        $elements = $this->elements;
        foreach ($elements as $key => $element) {
            if (!in_array($key, $routeCodeList)) {
                unset($this->elements[$key]);
            }
        }

        return $this;
    }

    /**
     * Remove all routes.
     *
     * @return ApiRouteCollection
     */
    public function clear()
    {
        $this->elements = [];

        return $this;
    }

    /**
     * Convert a word in to the format for a symfony action action_name => actionName.
     *
     * @param string $action Word to actionify
     *
     * @return string Actionified word
     */
    public function actionify($action)
    {
        $action .= 'Action';

        return lcfirst(str_replace(' ', '', ucwords(strtr($action, '_-', '  '))));
    }

    /**
     * @return string
     */
    protected function getBaseControllerName()
    {
        return $this->api->getBaseControllerName();
    }

    /**
     * @return string
     */
    protected function getBaseRouteName()
    {
        return $this->api->getBaseRouteName();
    }

    /**
     * @return string
     */
    protected function getBaseRoutePattern()
    {
        return $this->api->getBaseRoutePattern();
    }

    /**
     * @param $element
     *
     * @return Route
     */
    private function resolve($element)
    {
        if (is_callable($element)) {
            return call_user_func($element);
        }

        return $element;
    }
}