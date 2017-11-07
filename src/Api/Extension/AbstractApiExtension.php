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

abstract class AbstractApiExtension implements ApiExtensionInterface
{
    /**
     * @inheritDoc
     */
    public function configureQuery(RestApiInterface $api, QueryBuilder $query)
    {
        // TODO: Implement configureQuery() method.
    }

    /**
     * @inheritDoc
     */
    public function preUpdate(RestApiInterface $api, $object)
    {
        // TODO: Implement preUpdate() method.
    }

    /**
     * @inheritDoc
     */
    public function postUpdate(RestApiInterface $api, $object)
    {
        // TODO: Implement postUpdate() method.
    }

    /**
     * @inheritDoc
     */
    public function prePersist(RestApiInterface $api, $object)
    {
        // TODO: Implement prePersist() method.
    }

    /**
     * @inheritDoc
     */
    public function postPersist(RestApiInterface $api, $object)
    {
        // TODO: Implement postPersist() method.
    }

    /**
     * @inheritDoc
     */
    public function preRemove(RestApiInterface $api, $object)
    {
        // TODO: Implement preRemove() method.
    }

    /**
     * @inheritDoc
     */
    public function postRemove(RestApiInterface $api, $object)
    {
        // TODO: Implement postRemove() method.
    }

    /**
     * @inheritDoc
     */
    public function configureRoutes(RestApiInterface $api, ApiRouteCollection $collection)
    {
        // TODO: Implement configureRoutes() method.
    }

    /**
     * @inheritDoc
     */
    public function alterObject(RestApiInterface $api, $object)
    {
        // TODO: Implement alterObject() method.
    }
}