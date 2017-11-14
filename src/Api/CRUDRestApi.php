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

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Ynlo\RestfulPlatformBundle\Api\SwaggerHelper\SWResponseHelper;
use Ynlo\RestfulPlatformBundle\Collection\FilterCollection;
use Ynlo\RestfulPlatformBundle\Routing\ApiRouteCollection;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\SWOperation;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\SWParameter;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\SWResponse;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\SWSchema;

class CRUDRestApi extends AbstractRestApi
{
    /**
     * @return array
     */
    public function listOperation()
    {
        $exampleLinks = [
            'self' => [
                'href' => $this->generateUrl('list', ['page' => 1, 'limit' => 30]),
            ],
            'next' => [
                'href' => $this->generateUrl('list', ['page' => 2, 'limit' => 30]),
            ],
            'last' => [
                'href' => $this->generateUrl('list', ['page' => 3, 'limit' => 30]),
            ],
        ];

        $specs = [
            SWOperation::parameterInQuery('q', 'string'),
            SWOperation::parameterInQuery('page', 'number'),
            SWOperation::parameterInQuery('limit', 'number'),
            SWOperation::description(sprintf('%s List', $this->getLabel())),
            SWResponseHelper::paginatedCollection(
                $this->getResourceClass(),
                $this->getLabel(),
                $exampleLinks,
                $this->getCRUDSerializerGroups('list', true)
            ),
        ];

        $collection = new FilterCollection();
        $this->configureListFilters($collection);
        $filters = $collection->getFilters();
        foreach ($filters as $filter) {
            $specs[] = SWOperation::parameterInQuery(
                $filter->getParameter(),
                $filter->getType(),
                [
                    SWSchema::description($filter->getDescription()),
                    SWSchema::example($filter->getExample()),
                ]
            );
        }

        return $specs;
    }

    public function getOperation()
    {
        return [
            SWOperation::description(sprintf('Fetch %s', $this->getLabel())),
            SWOperation::parameter(
                $this->getIdParameter(),
                [
                    SWParameter::inPath('string'),
                    SWParameter::description($this->getLabel().' identifier'),
                ]
            ),
            SWResponseHelper::success(
                [
                    SWResponse::model($this->getResourceClass(), $this->getCRUDSerializerGroups('get', true)),
                ]
            ),
            SWResponseHelper::notFound(),
        ];
    }

    public function createOperation()
    {
        return [
            SWOperation::description(sprintf('Create %s', $this->getLabel())),
            SWOperation::model($this->getResourceClass(), $this->getCRUDSerializerGroups('create', false)),
            SWResponseHelper::success(
                [
                    SWResponse::model($this->getResourceClass(), $this->getCRUDSerializerGroups('create', true)),
                ],
                Response::HTTP_CREATED
            ),
            SWResponseHelper::validationError(),
        ];
    }

    public function updateOperation()
    {
        return [
            SWOperation::description(sprintf('Update %s', $this->getLabel())),
            SWOperation::model($this->getResourceClass(), $this->getCRUDSerializerGroups('update', false)),
            SWOperation::parameter(
                $this->getIdParameter(),
                [
                    SWParameter::inPath('string'),
                    SWParameter::description($this->getLabel().' identifier'),
                ]
            ),
            SWResponseHelper::success(
                [
                    SWResponse::model($this->getResourceClass(), $this->getCRUDSerializerGroups('update', true)),
                ]
            ),
            SWResponseHelper::validationError(),
            SWResponseHelper::notFound(),
        ];
    }

    public function removeOperation()
    {
        return [
            SWOperation::description(sprintf('Delete %s', $this->getLabel())),
            SWOperation::parameter(
                $this->getIdParameter(),
                [
                    SWParameter::inPath('string'),
                    SWParameter::description($this->getLabel().' identifier'),
                ]
            ),
            SWResponseHelper::success(),
            SWResponseHelper::notFound(),
        ];
    }

    public function configureListFilters(FilterCollection $filters)
    {

    }

    /**
     * @return array
     */
    public function configureSearchFields()
    {
        /** @var EntityManager $modelManager */
        $em = $this->getManager();
        $metadata = $em->getClassMetadata($this->getResourceClass());

        return $metadata->getFieldNames();
    }

    protected function configureRoutes(ApiRouteCollection $routes)
    {
        $this->routes->add(Request::METHOD_GET, 'list');
        $this->routes->add(Request::METHOD_POST, 'create');
        $this->routes->add(Request::METHOD_GET, 'get', $this->getRouterIdParameter());
        $this->routes->add(Request::METHOD_PUT, 'update', $this->getRouterIdParameter());
        $this->routes->add(Request::METHOD_DELETE, 'remove', $this->getRouterIdParameter());
    }

    /**
     * @param string  $operation
     * @param boolean $isResponse
     *
     * @return array
     */
    public function getCRUDSerializerGroups($operation, $isResponse)
    {
        return [];
    }
}