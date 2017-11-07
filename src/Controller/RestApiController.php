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

namespace Ynlo\RestfulPlatformBundle\Controller;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\Expr\Orx;
use Doctrine\ORM\QueryBuilder;
use Hateoas\Representation\PaginatedRepresentation;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Ynlo\RestfulPlatformBundle\Api\CRUDRestApi;
use Ynlo\RestfulPlatformBundle\Api\RestApiInterface;
use Ynlo\RestfulPlatformBundle\Collection\FilterCollection;
use Ynlo\RestfulPlatformBundle\Error\ValidationError;

class RestApiController extends Controller implements RestApiControllerInterface
{
    /**
     * @var RestApiInterface
     */
    protected $api;

    /**
     * @return RestApiInterface
     */
    public function getApi()
    {
        return $this->api;
    }

    /**
     * @param RestApiInterface $api
     */
    public function setApi(RestApiInterface $api)
    {
        $this->api = $api;
    }

    /**
     * @param Request $request
     *
     * @return PaginatedRepresentation
     */
    public function listAction(Request $request)
    {
        $qb = $this->api->createQuery();

        if ($q = $request->get('q')) {
            $this->search($qb, (array) $q);
        }

        if ($this->api instanceof CRUDRestApi) {
            $collection = new FilterCollection();
            $this->api->configureListFilters($collection);
            foreach ($collection->getFilters() as $filter) {
                if ($value = $request->query->get($filter->getParameter())) {
                    $field = $filter->getField();

                    if (is_string($field)) {
                        if (strpos($field, '.') === false) {
                            $alias = $qb->getRootAliases()[0];
                            $field = "$alias.$field";
                        }
                        $parameterName = 'filter_'.$filter->getParameter();

                        switch ($filter->getType()) {
                            case 'number':
                            case 'int':
                            case 'integer':
                            case 'float':
                            case 'decimal':
                                $type = \PDO::PARAM_INT;
                                break;
                            case 'bool':
                            case 'boolean':
                                $type = \PDO::PARAM_BOOL;
                                break;
                            default:
                                $type = \PDO::PARAM_STR;
                        }

                        $qb->andWhere("$field = :$parameterName")
                           ->setParameter($parameterName, $value, $type);
                    }else{
                        $field($qb, $value);
                    }
                }
            }
        }

        $paginator = $this->get('restful_platform.resource_orm_paginator');
        $paginator->handleRequest($request);

        return $paginator->paginate($qb);
    }

    /**
     * Filter some columns with simple string.
     *
     * @param QueryBuilder $qb
     * @param array        $search array of string to search
     *
     * @return array
     */
    protected function search(QueryBuilder $qb, $search)
    {
        $alias = $qb->getRootAliases()[0];
        $configureSearchFields = $this->api->configureSearchFields();
        $meta = $qb->getEntityManager()->getClassMetadata($qb->getRootEntities()[0]);

        if (count($configureSearchFields) > 0) {
            foreach ($search as $q) {
                $id = mt_rand();
                $orx = new Orx();
                foreach ($configureSearchFields as $field) {
                    if (strpos($field, '.') !== false && !isset($meta->embeddedClasses[explode('.', $field)[0]])) {
                        $orx->add("$field LIKE :search_$id");
                    } else { //append current alias
                        $orx->add("$alias.$field LIKE :search_$id");
                    }
                }
                $qb->andWhere($orx)->setParameter("search_$id", "%$q%");
            }
        }
    }

    /**
     * @return mixed
     */
    public function createAction()
    {
        $object = $this->getSubject();
        if (!$object) {
            return Response::HTTP_BAD_REQUEST;
        }

        if ($this->getManager()->contains($object)) {
            return Response::HTTP_CONFLICT;
        }

        $violations = $this->api->validate($object);
        if ($violations->count()) {
            return [Response::HTTP_UNPROCESSABLE_ENTITY, new ValidationError($violations)];
        }

        $this->api->create($object);

        return [Response::HTTP_CREATED, $object];
    }

    /**
     * @return int|object
     */
    public function getAction()
    {
        if (!$this->getSubject()) {
            return Response::HTTP_NOT_FOUND;
        }

        return $this->api->getSubject();
    }

    /**
     * @return mixed
     */
    public function updateAction()
    {
        $object = $this->getSubject();
        if (!$object) {
            return Response::HTTP_NOT_FOUND;
        }

        $violations = $this->api->validate($object);
        if ($violations->count()) {
            return [Response::HTTP_UNPROCESSABLE_ENTITY, new ValidationError($violations)];
        }

        $this->api->update($object);

        return $object;
    }

    /**
     * @return int
     */
    public function removeAction()
    {
        if (!$this->getSubject()) {
            return Response::HTTP_NOT_FOUND;
        }

        $this->api->remove($this->getSubject());

        return Response::HTTP_NO_CONTENT;
    }

    /**
     * @return object
     */
    protected function getSubject()
    {
        return $this->api->getSubject();
    }

    /**
     * @return EntityManager|ObjectManager
     */
    protected function getManager()
    {
        return $this->api->getManager();
    }
}