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

namespace Ynlo\RestfulPlatformBundle\Api\Extension;

use Doctrine\ORM\QueryBuilder;
use Ynlo\RestfulPlatformBundle\Api\RestApiInterface;
use Ynlo\RestfulPlatformBundle\Routing\ApiRouteCollection;

interface ApiExtensionInterface
{
    /**
     * @param RestApiInterface $api
     * @param QueryBuilder     $query
     */
    public function configureQuery(RestApiInterface $api, QueryBuilder $query);

    /**
     * @param RestApiInterface $api
     * @param mixed            $object
     */
    public function preUpdate(RestApiInterface $api, $object);

    /**
     * @param RestApiInterface $api
     * @param mixed            $object
     */
    public function postUpdate(RestApiInterface $api, $object);

    /**
     * @param RestApiInterface $api
     * @param mixed            $object
     */
    public function prePersist(RestApiInterface $api, $object);

    /**
     * @param RestApiInterface $api
     * @param mixed            $object
     */
    public function postPersist(RestApiInterface $api, $object);

    /**
     * @param RestApiInterface $api
     * @param mixed            $object
     */
    public function preRemove(RestApiInterface $api, $object);

    /**
     * @param RestApiInterface $api
     * @param mixed            $object
     */
    public function postRemove(RestApiInterface $api, $object);

    /**
     * @param RestApiInterface   $api
     * @param ApiRouteCollection $collection
     */
    public function configureRoutes(RestApiInterface $api, ApiRouteCollection $collection);

    /**
     * Get a chance to modify object instance.
     *
     * @param RestApiInterface $api
     * @param mixed            $object
     */
    public function alterObject(RestApiInterface $api, $object);
}