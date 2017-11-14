<?php
/**
 * LICENSE: This file is subject to the terms and conditions defined in
 * file 'LICENSE', which is part of this source code package.
 *
 * @copyright 2017 Copyright(c) - All rights reserved.
 * @author    YNLO-Ultratech Development Team <developer@ynloultratech.com>
 * @package   restful-platform
 */

namespace Ynlo\RestfulPlatformBundle\Tests\Controller;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\Expr\Orx;
use Doctrine\ORM\QueryBuilder;
use Hateoas\Representation\PaginatedRepresentation;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Ynlo\RestfulPlatformBundle\Tests\Fixtures\Model\User;
use Ynlo\RestfulPlatformBundle\Api\CRUDRestApi;
use Ynlo\RestfulPlatformBundle\Collection\FilterCollection;
use Ynlo\RestfulPlatformBundle\Controller\RestApiController;
use Ynlo\RestfulPlatformBundle\Error\ValidationError;
use Ynlo\RestfulPlatformBundle\Paginator\ORMPaginator;

class RestApiControllerTest extends TestCase
{
    use MockeryPHPUnitIntegration;

    /**
     * @var ContainerInterface|m\MockInterface
     */
    protected $container;

    /**
     * @var RestApiController
     */
    protected $controller;

    /**
     * @var CRUDRestApi|m\MockInterface
     */
    protected $api;

    protected function setUp()
    {
        $this->container = m::mock(ContainerInterface::class);
        $this->api = m::mock(CRUDRestApi::class);

        $this->controller = new RestApiController();
        $this->controller->setContainer($this->container);
        $this->controller->setApi($this->api);
    }

    public function testListAction()
    {
        $request = new Request();
        $request->query->set('q', 'foobar');
        $request->query->set('foo', 'foo...');
        $request->query->set('bar', 'bar...');
        $request->query->set('foobar', '1');
        $request->query->set('foo_foo', 'foooo');

        $qb = m::mock(QueryBuilder::class);
        $this->api->shouldReceive('configureSearchFields')->andReturn(['foo', 'bar', 'foo.bar']);

        $em = m::mock(EntityManager::class);
        $qb->shouldReceive('getEntityManager')->andReturn($em);
        $qb->shouldReceive('getRootAliases')->andReturn(['o']);
        $qb->shouldReceive('getRootEntities')->andReturn([User::class]);

        //search
        $qb->shouldReceive('andWhere')->withArgs(
            function ($arg) {
                if ($arg instanceof Orx) {

                    $match = preg_match('/^o\.foo LIKE :search_\d+/', $arg->getParts()[0])
                             || preg_match('/^o\.bar LIKE :search_\d+/', $arg->getParts()[0])
                             || preg_match('/^foobar\.bar LIKE :search_\d+/', $arg->getParts()[0]);

                    self::assertTrue($match);

                    return true;
                }

                return false;
            }
        )->andReturnSelf();
        $qb->shouldReceive('setParameter')->withArgs(
            function ($arg1, $arg2) {
                return strpos($arg1, 'search_') === 0 && strpos($arg2, '%') === 0;
            }
        );

        //filters
        $qb->shouldReceive('andWhere')->withArgs(['o.foo = :filter_foo'])->andReturnSelf();
        $qb->shouldReceive('setParameter')->withArgs(['filter_foo', 'foo...', \PDO::PARAM_STR]);

        $qb->shouldReceive('andWhere')->withArgs(['o.bar = :filter_bar'])->andReturnSelf();
        $qb->shouldReceive('setParameter')->withArgs(['filter_bar', 'bar...', \PDO::PARAM_INT]);

        $qb->shouldReceive('andWhere')->withArgs(['o.foobar = :filter_foobar'])->andReturnSelf();
        $qb->shouldReceive('setParameter')->withArgs(['filter_foobar', '1', \PDO::PARAM_BOOL]);

        $qb->shouldReceive('andWhere')->withArgs(['o.foo_foo = :foo_foo'])->andReturnSelf();
        $qb->shouldReceive('setParameter')->withArgs(['foo_foo', 'foooo']);

        $meta = m::mock(ClassMetadata::class);

        $em->shouldReceive('getClassMetadata')->withArgs([User::class])->andReturn($meta);

        $this->api->shouldReceive('createQuery')->andReturn($qb);

        $this->api->shouldReceive('configureListFilters')
                  ->withArgs(
                      function (FilterCollection $collection) {
                          $collection->addFilter('foo');
                          $collection->addFilter('bar', 'bar', 'integer');
                          $collection->addFilter('foobar', 'foobar', 'bool');
                          $collection->addFilter(
                              'foo_foo',
                              function (QueryBuilder $qb, $value) {
                                  $qb->andWhere('o.foo_foo = :foo_foo')
                                     ->setParameter('foo_foo', 'foooo');
                              }
                          );

                          return $collection instanceof FilterCollection;
                      }
                  );

        $ExpectedResult = new PaginatedRepresentation([], 'route', [], 1, 1, 1);

        $paginator = m::mock(ORMPaginator::class);
        $paginator->shouldReceive('handleRequest')->withArgs([$request]);
        $paginator->shouldReceive('paginate')->withArgs([$qb])->andReturn($ExpectedResult);

        $this->container
            ->shouldReceive('get')
            ->withArgs(['restful_platform.resource_orm_paginator'])
            ->andReturn($paginator);

        $result = $this->controller->listAction($request);

        self::assertEquals($this->api, $this->controller->getApi());
        self::assertEquals($ExpectedResult, $result);
    }

    public function testCreateAction_WithoutSubject()
    {
        $this->api->shouldReceive('getSubject')->andReturn(null);
        self::assertEquals(Response::HTTP_BAD_REQUEST, $this->controller->createAction());
    }

    public function testCreateAction_WithSubjectButAlreadyExists()
    {
        $subject = new \stdClass();
        $this->api->shouldReceive('getSubject')->andReturn($subject);

        $manager = m::mock(EntityManagerInterface::class);
        $manager->shouldReceive('contains')->withArgs([$subject])->andReturn(true);

        $this->api->shouldReceive('getManager')->andReturn($manager);

        self::assertEquals(Response::HTTP_CONFLICT, $this->controller->createAction());
    }

    public function testCreateAction_WithSubjectValidationFailed()
    {
        $subject = new \stdClass();
        $this->api->shouldReceive('getSubject')->andReturn($subject);

        $manager = m::mock(EntityManagerInterface::class);
        $manager->shouldReceive('contains')->withArgs([$subject])->andReturn(false);

        $this->api->shouldReceive('getManager')->andReturn($manager);

        $violations = new ConstraintViolationList([new ConstraintViolation('error', '', [], $subject, 'foo', null)]);
        $this->api->shouldReceive('validate')->withArgs([$subject])->andReturn($violations);

        list($code, $error) = $this->controller->createAction();

        self::assertEquals(Response::HTTP_UNPROCESSABLE_ENTITY, $code);
        self::assertEquals(new ValidationError($violations), $error);
    }

    public function testCreateAction_CreateSuccess()
    {
        $subject = new \stdClass();
        $this->api->shouldReceive('getSubject')->andReturn($subject);

        $manager = m::mock(EntityManagerInterface::class);
        $manager->shouldReceive('contains')->withArgs([$subject])->andReturn(false);

        $this->api->shouldReceive('getManager')->andReturn($manager);

        $violations = new ConstraintViolationList([]);
        $this->api->shouldReceive('validate')->withArgs([$subject])->andReturn($violations);

        $this->api->shouldReceive('create')->withArgs([$subject]);

        list($code, $object) = $this->controller->createAction();

        self::assertEquals(Response::HTTP_CREATED, $code);
        self::assertEquals($subject, $object);
    }

    public function testGetAction_WithoutSubject()
    {
        $this->api->shouldReceive('getSubject')->andReturn(null);
        self::assertEquals(Response::HTTP_NOT_FOUND, $this->controller->getAction());
    }

    public function testGetAction_WithSubject()
    {
        $subject = new \stdClass();
        $this->api->shouldReceive('getSubject')->andReturn($subject);
        self::assertEquals($subject, $this->controller->getAction());
    }

    public function testUpdateAction_WithoutSubject()
    {
        $this->api->shouldReceive('getSubject')->andReturn(null);
        self::assertEquals(Response::HTTP_NOT_FOUND, $this->controller->updateAction());
    }

    public function testUpdateAction_ValidationFailed()
    {
        $subject = new \stdClass();
        $this->api->shouldReceive('getSubject')->andReturn($subject);
        $violations = new ConstraintViolationList([new ConstraintViolation('error', '', [], $subject, 'foo', null)]);
        $this->api->shouldReceive('validate')->withArgs([$subject])->andReturn($violations);

        list($code, $error) = $this->controller->updateAction();

        self::assertEquals(Response::HTTP_UNPROCESSABLE_ENTITY, $code);
        self::assertEquals(new ValidationError($violations), $error);
    }

    public function testUpdateAction_WithSubject()
    {
        $subject = new \stdClass();
        $this->api->shouldReceive('getSubject')->andReturn($subject);
        $violations = new ConstraintViolationList([]);
        $this->api->shouldReceive('validate')->withArgs([$subject])->andReturn($violations);
        $this->api->shouldReceive('update')->withArgs([$subject]);
        self::assertEquals($subject, $this->controller->updateAction());
    }

    public function testRemoveAction_WithoutSubject()
    {
        $this->api->shouldReceive('getSubject')->andReturn(null);
        self::assertEquals(Response::HTTP_NOT_FOUND, $this->controller->removeAction());
    }

    public function testRemoveAction_WithSubject()
    {
        $subject = new \stdClass();
        $this->api->shouldReceive('getSubject')->andReturn($subject);
        $this->api->shouldReceive('remove')->withArgs([$subject]);
        self::assertEquals(Response::HTTP_NO_CONTENT, $this->controller->removeAction());
    }
}
