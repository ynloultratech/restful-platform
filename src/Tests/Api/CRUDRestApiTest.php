<?php

namespace Ynlo\RestfulPlatformBundle\Tests\Api;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\ClassMetadata;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Ynlo\RestfulPlatformBundle\Tests\Fixtures\Model\User;
use Ynlo\RestfulPlatformBundle\Api\CRUDRestApi;
use PHPUnit\Framework\TestCase;
use Ynlo\RestfulPlatformBundle\Collection\FilterCollection;
use Ynlo\RestfulPlatformBundle\Swagger\Model\Operation;
use Ynlo\RestfulPlatformBundle\Swagger\Model\Parameter;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Common\SpecContainer;

class CRUDRestApiTest extends TestCase
{
    /**
     * @var CRUDRestApi
     */
    protected $api;

    /**
     * @var ContainerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $container;

    /**
     * @var Registry|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $doctrine;

    /**
     * @var EntityManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $manager;

    /**
     * @var EntityRepository|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $repository;

    protected function setUp()
    {
        $this->container = self::createMock(ContainerInterface::class);
        $this->doctrine = self::createMock(Registry::class);
        $this->manager = self::createMock(EntityManager::class);
        $this->repository = self::createMock(EntityRepository::class);

        $this->doctrine->method('getManager')->willReturn($this->manager);
        $this->manager->method('getRepository')->willReturn($this->repository);

        $this->api = new class extends CRUDRestApi
        {
            protected $resourceClass = User::class;
        };
        $this->api->setContainer($this->container);
    }

    public function testListOperation()
    {
        $api = new class extends CRUDRestApi
        {
            protected $resourceClass = User::class;

            public function generateUrl($name, $parameters = [], $absolute = UrlGeneratorInterface::ABSOLUTE_PATH)
            {
                return sprintf('/users?page=%s&limit=%s', $parameters['page'], $parameters['limit']);
            }

            public function configureListFilters(FilterCollection $filters)
            {
                $filters->addFilter('name', 'name', 'string', 'Filter by name');
            }
        };

        $operationSpecs = new SpecContainer($api->listOperation());
        $operation = new Operation();
        $decorator = $operationSpecs->getDecorator();
        $decorator($operation);

        self::assertEquals('User List', $operation->getDescription());

        self::assertEquals(Parameter::IN_QUERY, $operation->getParameters()->get('q')->getIn());
        self::assertEquals('string', $operation->getParameters()->get('q')->getType());

        self::assertEquals(Parameter::IN_QUERY, $operation->getParameters()->get('page')->getIn());
        self::assertEquals('number', $operation->getParameters()->get('page')->getType());

        self::assertEquals(Parameter::IN_QUERY, $operation->getParameters()->get('limit')->getIn());
        self::assertEquals('number', $operation->getParameters()->get('limit')->getType());

        self::assertEquals(Parameter::IN_QUERY, $operation->getParameters()->get('name')->getIn());
        self::assertEquals('string', $operation->getParameters()->get('name')->getType());
        self::assertEquals('Filter by name', $operation->getParameters()->get('name')->getDescription());

        $successResponse = $operation->getResponse(200);
        self::assertEquals('Successful Operation', $successResponse->getDescription());
        self::assertEquals('object', $successResponse->getSchema()->getProperty('_links')->getType());
        self::assertEquals('string', $successResponse->getSchema()->getProperty('_links')->getAdditionalProperties()->getType());
        self::assertEquals(
            [
                'self' => [
                    'href' => '/users?page=1&limit=30',
                ],
                'next' => [
                    'href' => '/users?page=2&limit=30',
                ],
                'last' => [
                    'href' => '/users?page=3&limit=30',
                ],
            ],
            $successResponse->getSchema()->getProperty('_links')->getExample()
        );
        self::assertEquals('integer', $successResponse->getSchema()->getProperty('page')->getType());
        self::assertEquals('integer', $successResponse->getSchema()->getProperty('limit')->getType());
        self::assertEquals('integer', $successResponse->getSchema()->getProperty('pages')->getType());
        self::assertEquals('integer', $successResponse->getSchema()->getProperty('total')->getType());
        self::assertEquals('array', $successResponse->getSchema()->getProperty('items')->getType());
        self::assertEquals(User::class, $successResponse->getSchema()->getProperty('items')->getItems()->getClass());
    }

    public function testGetOperation()
    {
        $api = new class extends CRUDRestApi
        {
            protected $resourceClass = User::class;
        };

        $operationSpecs = new SpecContainer($api->getOperation());
        $operation = new Operation();
        $decorator = $operationSpecs->getDecorator();
        $decorator($operation);

        self::assertEquals('Fetch User', $operation->getDescription());

        self::assertEquals(Parameter::IN_PATH, $operation->getParameters()->get('userId')->getIn());
        self::assertEquals('string', $operation->getParameters()->get('userId')->getType());
        self::assertEquals('User identifier', $operation->getParameters()->get('userId')->getDescription());

        $successResponse = $operation->getResponse(200);
        self::assertEquals('Successful Operation', $successResponse->getDescription());
        self::assertEquals(User::class, $successResponse->getSchema()->getClass());

        $notFoundResponse = $operation->getResponse(404);
        self::assertEquals('Not Found', $notFoundResponse->getDescription());
    }

    public function testCreateOperation()
    {
        $api = new class extends CRUDRestApi
        {
            protected $resourceClass = User::class;
        };

        $operationSpecs = new SpecContainer($api->createOperation());
        $operation = new Operation();
        $decorator = $operationSpecs->getDecorator();
        $decorator($operation);

        self::assertEquals('Create User', $operation->getDescription());

        self::assertEquals(Parameter::IN_BODY, $operation->getParameters()->get('body')->getIn());
        self::assertEquals(User::class, $operation->getParameters()->get('body')->getSchema()->getClass());

        $successResponse = $operation->getResponse(201);
        self::assertEquals('Successful Operation', $successResponse->getDescription());
        self::assertEquals(User::class, $successResponse->getSchema()->getClass());

        $errorResponse = $operation->getResponse(422);
        self::assertEquals('Validation Error', $errorResponse->getDescription());
    }

    public function testUpdateOperation()
    {
        $api = new class extends CRUDRestApi
        {
            protected $resourceClass = User::class;
        };

        $operationSpecs = new SpecContainer($api->updateOperation());
        $operation = new Operation();
        $decorator = $operationSpecs->getDecorator();
        $decorator($operation);

        self::assertEquals('Update User', $operation->getDescription());

        self::assertEquals(Parameter::IN_PATH, $operation->getParameters()->get('userId')->getIn());
        self::assertEquals('string', $operation->getParameters()->get('userId')->getType());
        self::assertEquals('User identifier', $operation->getParameters()->get('userId')->getDescription());

        self::assertEquals(Parameter::IN_BODY, $operation->getParameters()->get('body')->getIn());
        self::assertEquals(User::class, $operation->getParameters()->get('body')->getSchema()->getClass());

        $successResponse = $operation->getResponse(200);
        self::assertEquals('Successful Operation', $successResponse->getDescription());
        self::assertEquals(User::class, $successResponse->getSchema()->getClass());

        $errorResponse = $operation->getResponse(422);
        self::assertEquals('Validation Error', $errorResponse->getDescription());
    }

    public function testRemoveOperation()
    {
        $api = new class extends CRUDRestApi
        {
            protected $resourceClass = User::class;
        };

        $operationSpecs = new SpecContainer($api->removeOperation());
        $operation = new Operation();
        $decorator = $operationSpecs->getDecorator();
        $decorator($operation);

        self::assertEquals('Delete User', $operation->getDescription());

        self::assertEquals(Parameter::IN_PATH, $operation->getParameters()->get('userId')->getIn());
        self::assertEquals('string', $operation->getParameters()->get('userId')->getType());
        self::assertEquals('User identifier', $operation->getParameters()->get('userId')->getDescription());

        $successResponse = $operation->getResponse(200);
        self::assertEquals('Successful Operation', $successResponse->getDescription());

        $errorResponse = $operation->getResponse(404);
        self::assertEquals('Not Found', $errorResponse->getDescription());
    }

    public function testConfigureSearchFields()
    {
        $this->container->expects(self::any())
                        ->method('get')
                        ->with('doctrine')
                        ->willReturn($this->doctrine);

        $fields = ['username', 'firstName', 'lastName'];
        $metadata = self::createMock(ClassMetadata::class);
        $metadata->expects(self::once())->method('getFieldNames')->willReturn($fields);

        $this->manager->expects(self::any())
                      ->method('getClassMetadata')
                      ->with(User::class)
                      ->willReturn($metadata);

        $searchFields = $this->api->configureSearchFields();
        self::assertEquals($fields, $searchFields);
    }
}
