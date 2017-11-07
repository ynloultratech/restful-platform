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

namespace Ynlo\RestfulPlatformBundle\Api;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Ynlo\RestfulPlatformBundle\Api\Extension\ApiExtensionInterface;
use Ynlo\RestfulPlatformBundle\Routing\ApiRouteCollection;

interface RestApiInterface extends ContainerAwareInterface
{

    /**
     * Name of the parameter to retrieve the resource identifier from request
     *
     * @return string
     */
    public function getIdParameter();

    /**
     * Name of the parameter to use in route generation,
     * same like `getIdParameter()` but wrapped with {}
     *
     * @return string
     */
    public function getRouterIdParameter();

    /**
     * Name of the field in the DB to retrieve the resource,
     * commonly is the `id` but can use other to use `slugs`
     *
     * @return string
     */
    public function getIdField();

    /**
     * @return string
     */
    public function getLabel();

    /**
     * @return string
     */
    public function getResourceClass();

    /**
     * @return string
     */
    public function getBaseControllerName();

    /**
     * @return string
     */
    public function getBaseRouteName();

    /**
     * @return string
     */
    public function getBaseRoutePattern();

    /**
     * Returns the list of available urls.
     *
     * @return ApiRouteCollection the list of available urls
     */
    public function getRoutes();

    /**
     * Returns true if the route $name is available.
     *
     * @param string $name
     *
     * @return bool
     */
    public function hasRoute($name);

    /**
     * @return ObjectManager|EntityManager
     */
    public function getManager();

    /**
     * Create the query build to fetch results
     *
     * @return QueryBuilder
     */
    public function createQuery();

    /**
     * @param Request $request
     */
    public function setRequest(Request $request);

    /**
     * @return Request
     */
    public function getRequest();

    /**
     * @param string  $name
     * @param array   $parameters
     * @param integer $absolute
     *
     * @return mixed
     */
    public function generateUrl($name, $parameters = [], $absolute = UrlGeneratorInterface::ABSOLUTE_PATH);

    /**
     * @param string  $name
     * @param object  $resource
     * @param array   $parameters
     * @param integer $absolute
     *
     * @return mixed
     */
    public function generateResourceUrl($name, $resource, $parameters = [], $absolute = UrlGeneratorInterface::ABSOLUTE_PATH);

    /**
     * Get current resource in create, update & remove operations
     *
     * @return object
     */
    public function getSubject();

    /**
     * @param mixed $object
     *
     * @return mixed
     */
    public function update($object);

    /**
     * @param mixed $object
     *
     * @return mixed
     */
    public function create($object);

    /**
     * @param mixed $object
     */
    public function remove($object);

    /**
     * @param object $object
     *
     * @return ConstraintViolationListInterface
     */
    public function validate($object);

    /**
     * Resolve resource for given identifier, slug or id
     *
     * @param mixed $identifier
     *
     * @return mixed
     */
    public function getResource($identifier);

    /**
     * @param mixed $object
     */
    public function preValidate($object);

    /**
     * @param mixed $object
     */
    public function preUpdate($object);

    /**
     * @param mixed $object
     */
    public function postUpdate($object);

    /**
     * @param mixed $object
     */
    public function prePersist($object);

    /**
     * @param mixed $object
     */
    public function postPersist($object);

    /**
     * @param mixed $object
     */
    public function preRemove($object);

    /**
     * @param mixed $object
     */
    public function postRemove($object);

    /**
     * @return ApiExtensionInterface[]
     */
    public function getExtensions();

    /**
     * @param ApiExtensionInterface $extension
     *
     * @return RestApiInterface
     */
    public function addExtension(ApiExtensionInterface $extension);
}