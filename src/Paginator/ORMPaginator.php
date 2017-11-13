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

namespace Ynlo\RestfulPlatformBundle\Paginator;


use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use Hateoas\Representation\PaginatedRepresentation;
use Knp\Component\Pager\Pagination\AbstractPagination;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;

class ORMPaginator
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var PaginatorInterface
     */
    protected $paginator;

    /**
     * @var callable[]
     */
    protected $qbModifiers = [];

    /**
     * @var string
     */
    protected $route;

    /**
     * @var array
     */
    protected $routeParameters = [];

    /**
     * @var string
     */
    protected $pageParamName = 'page';

    /**
     * @var string
     */
    protected $limitParamName = 'limit';

    /**
     * @var integer
     */
    protected $page = 1;

    /**
     * @var integer
     */
    protected $limit = 30;

    /**
     * ApiPaginator constructor.
     *
     * @param Registry      $registry
     * @param PaginatorInterface $paginator
     */
    public function __construct(Registry $registry, PaginatorInterface $paginator)
    {
        $this->em = $registry->getManager();
        $this->paginator = $paginator;
    }

    /**
     * @param int $page
     */
    public function setPage(int $page)
    {
        $this->page = $page;
    }

    /**
     * @param int $limit
     */
    public function setLimit(int $limit)
    {
        $this->limit = $limit;
    }

    /**
     * Used to add some extra logic to the query builder
     * The query builder alias is 'o' by default
     *
     * @param callable $modifier
     *
     * @return $this
     */
    public function modifyQueryBuilder(callable $modifier)
    {
        $this->qbModifiers[] = $modifier;

        return $this;
    }

    /**
     * Specifies an ordering for the query results.
     * Replaces any previously specified orderings, if any.
     *
     * @param string $sort  The ordering expression.
     * @param string $order The ordering direction.
     *
     * @return $this
     */
    public function orderBy($sort, $order = null)
    {
        $this->modifyQueryBuilder(
            function (QueryBuilder $queryBuilder) use ($sort, $order) {
                $queryBuilder->orderBy($this->resolveQueryField($queryBuilder, $sort), $order);
            }
        );

        return $this;
    }

    /**
     * Handle request to get parameters from request like pagination, orderBy and filters
     *
     * @param Request $request
     *
     * @return $this
     */
    public function handleRequest(Request $request)
    {
        if (($limit = $request->get($this->limitParamName, $this->limit)) && $limit > 0 && $limit <= 100) {
            $this->limit = $request->get($this->limitParamName, $this->limit);
        }
        $this->page = $request->get($this->pageParamName, $this->page);
        $this->route = $request->get('_route');
        $this->routeParameters = $request->get('_route_params');

        return $this;
    }

    /**
     * @param QueryBuilder $qb
     *
     * @return PaginatedRepresentation
     */
    public function paginate($qb)
    {
        $pagination = $this->paginator->paginate($qb, $this->page, $this->limit);

        return $this->createPaginatedRepresentation($pagination);
    }

    /**
     * @param PaginationInterface $pagination
     *
     * @return PaginatedRepresentation
     */
    protected function createPaginatedRepresentation(PaginationInterface $pagination)
    {
        /** @var AbstractPagination $pagination */
        $pages = ceil($pagination->getTotalItemCount() / $this->limit);
        $records = new PaginatedCollection($pagination->getItems());

        return new PaginatedRepresentation(
            $records,
            $this->route,
            $this->routeParameters,
            $this->page,
            $this->limit,
            $pages,
            $this->pageParamName,
            $this->limitParamName,
            false,
            $pagination->getTotalItemCount()
        );
    }

    /**
     * Resolve the name of field for given QB,
     * e.g. 'name' => 'o.name'
     *
     * @param QueryBuilder $qb
     * @param string       $field
     *
     * @return string
     */
    private function resolveQueryField(QueryBuilder $qb, $field)
    {
        if (is_string($field) && strpos($field, '.') === false) {
            $alias = $qb->getRootAliases()[0];
            $field = implode('.', [$alias, $field]);
        }

        return $field;
    }
}