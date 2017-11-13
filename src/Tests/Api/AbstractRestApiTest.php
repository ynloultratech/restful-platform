<?php

namespace Ynlo\RestfulPlatformBundle\Tests\Api;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use JMS\Serializer\DeserializationContext;
use JMS\Serializer\SerializerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\Router;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Ynlo\RestfulPlatformBundle\Tests\Fixtures\Model\User;
use Ynlo\RestfulPlatformBundle\Api\AbstractRestApi;
use PHPUnit\Framework\TestCase;
use Ynlo\RestfulPlatformBundle\Api\Extension\AbstractApiExtension;
use Ynlo\RestfulPlatformBundle\Api\Extension\ApiExtensionInterface;
use Ynlo\RestfulPlatformBundle\Api\RestApiInterface;
use Ynlo\RestfulPlatformBundle\Api\RestApiSpecification;
use Ynlo\RestfulPlatformBundle\Controller\RestApiController;
use Ynlo\RestfulPlatformBundle\Exception\ApiError;
use Ynlo\RestfulPlatformBundle\Routing\ApiRouteCollection;

class AbstractRestApiTest extends TestCase
{
    /**
     * @var AbstractRestApi
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

        $this->api = new class extends AbstractRestApi
        {
            protected $resourceClass = User::class;

            public $calledEvents = [];

            protected function configureRoutes(ApiRouteCollection $routes)
            {
                $routes->add('delete', 'delete');
            }

            public function preValidate($object)
            {
                $this->calledEvents[] = __FUNCTION__;
            }

            public function preUpdate($object)
            {
                $this->calledEvents[] = __FUNCTION__;
            }

            public function postUpdate($object)
            {
                $this->calledEvents[] = __FUNCTION__;
            }

            public function prePersist($object)
            {
                $this->calledEvents[] = __FUNCTION__;
            }

            public function postPersist($object)
            {
                $this->calledEvents[] = __FUNCTION__;
            }

            public function preRemove($object)
            {
                $this->calledEvents[] = __FUNCTION__;
            }

            public function postRemove($object)
            {
                $this->calledEvents[] = __FUNCTION__;
            }
        };
        $this->api->setContainer($this->container);
    }

    public function testConstructor()
    {
        self::assertEquals('User', $this->api->getLabel());
        self::assertEquals('user', $this->api->getBaseRouteName());
        self::assertEquals('/users', $this->api->getBaseRoutePattern());
        self::assertEquals('RestfulPlatformBundle:RestApi', $this->api->getBaseControllerName());

        self::expectException(\LogicException::class);
        new class extends AbstractRestApi
        {
            protected function configureRoutes(ApiRouteCollection $routes)
            {

            }
        };
    }

    public function testResourceClass()
    {
        $value = User::class;
        self::assertEquals($value, $this->api->setResourceClass($value)->getResourceClass());
    }

    public function testLabel()
    {
        $value = 'User';
        self::assertEquals($value, $this->api->setLabel($value)->getLabel());
    }

    public function testBaseControllerName()
    {
        $value = RestApiController::class;
        self::assertEquals($value, $this->api->setBaseControllerName($value)->getBaseControllerName());
    }

    public function testBaseRouteName()
    {
        $value = 'user';
        self::assertEquals($value, $this->api->setBaseRouteName($value)->getBaseRouteName());
    }

    public function testBaseRoutePattern()
    {
        $value = '/users';
        self::assertEquals($value, $this->api->setBaseRoutePattern($value)->getBaseRoutePattern());
    }

    public function testGetIdParameter()
    {
        self::assertEquals('userId', $this->api->getIdParameter());
    }

    public function testGetIdRouterParameter()
    {
        self::assertEquals('{userId}', $this->api->getRouterIdParameter());
    }

    public function testGetIdField()
    {
        self::assertEquals('id', $this->api->getIdField());
    }

    public function testSubject()
    {
        $value = new \stdClass();
        self::assertEquals($value, $this->api->setSubject($value)->getSubject());
    }

    public function testGetResource()
    {
        $object = new \stdClass();
        $this->container->expects(self::any())->method('get')->with('doctrine')->willReturn($this->doctrine);
        $this->repository->method('findOneBy')->with(['id' => 1])->willReturn($object);

        self::assertEquals($object, $this->api->getResource(1));
    }

    public function testRequest()
    {
        $value = new Request();
        self::assertEquals($value, $this->api->setRequest($value)->getRequest());
    }

    public function testRequest_GetFromCurrentRequest()
    {
        $value = new Request();
        $stack = self::createMock(RequestStack::class);

        $stack->expects(self::once())
              ->method('getCurrentRequest')
              ->willReturn($value);

        $this->container
            ->expects(self::once())
            ->method('get')
            ->with('request_stack')
            ->willReturn($stack);

        self::assertEquals($value, $this->api->getRequest());
    }

    public function testRequest_CreateFromGlobals()
    {
        $value = Request::createFromGlobals();
        $stack = self::createMock(RequestStack::class);

        $stack->expects(self::once())
              ->method('getCurrentRequest')
              ->willReturn(null);

        $this->container
            ->expects(self::once())
            ->method('get')
            ->with('request_stack')
            ->willReturn($stack);

        self::assertEquals($value, $this->api->getRequest());
    }

    public function testHasRoute()
    {
        $this->api->getRoutes()->add(Request::METHOD_GET, 'get');
        self::assertTrue($this->api->hasRoute('get'));
        self::assertFalse($this->api->hasRoute('create'));
    }

    public function testGenerateUrl()
    {
        $router = self::createMock(Router::class);

        $router->expects(self::once())
               ->method('generate')
               ->with(
                   'user_get',
                   [
                       'id' => 1,
                       'version' => 'v1',
                   ],
                   UrlGeneratorInterface::ABSOLUTE_PATH
               )->willReturn('/v1/user/1');

        $this->container
            ->expects(self::once())
            ->method('get')
            ->with('router')
            ->willReturn($router);

        $this->api->setRequest(Request::createFromGlobals());
        $this->api->getRoutes()->add(Request::METHOD_GET, 'get', '{id}');

        $url = $this->api->generateUrl('get', ['id' => 1], UrlGeneratorInterface::ABSOLUTE_PATH);
        self::assertEquals('/v1/user/1', $url);
    }

    public function testGenerateResourceUrl()
    {
        //TODO: implement
        $url = $this->api->generateResourceUrl('get', new \stdClass());
        self::assertNull($url);
    }

    public function testGetRoutes()
    {
        $routes = $this->api->getRoutes();
        self::assertNotEmpty($routes->getElements());
        self::assertEquals($routes, $this->api->getRoutes());
        self::assertTrue($this->api->hasRoute('delete'));
        self::assertFalse($this->api->hasRoute('create'));

        $routes->add(Request::METHOD_POST, 'create');
        self::assertTrue($this->api->getRoutes()->has('create'));
    }

    public function testGetManager()
    {
        $this->container->expects(self::any())
                        ->method('get')
                        ->with('doctrine')
                        ->willReturn($this->doctrine);

        $manager = $this->api->getManager();
        self::assertEquals($this->manager, $manager);
    }

    public function testCreateQuery()
    {
        $this->container->expects(self::any())
                        ->method('get')
                        ->with('doctrine')
                        ->willReturn($this->doctrine);

        $qb = self::createMock(QueryBuilder::class);
        $qb->expects(self::once())->method('andWhere')->with('1 = 1');

        $extension = new class extends AbstractApiExtension
        {
            public function configureQuery(RestApiInterface $api, QueryBuilder $query)
            {
                $query->andWhere('1 = 1');
            }
        };
        $this->api->addExtension($extension);

        $this->repository->expects(self::any())
                         ->method('createQueryBuilder')
                         ->with('o')
                         ->willReturn($qb);

        $qbCreated = $this->api->createQuery();
        self::assertEquals($qb, $qbCreated);
    }

    public function testUpdate()
    {
        $object = new \stdClass();

        $this->container->expects(self::any())
                        ->method('get')
                        ->with('doctrine')
                        ->willReturn($this->doctrine);

        $this->manager->expects(self::once())->method('flush')->with($object);
        $this->manager->expects(self::once())->method('refresh')->with($object);

        $extension = self::createMock(ApiExtensionInterface::class);
        $extension->expects(self::once())->method('preUpdate')->with($this->api, $object);
        $extension->expects(self::once())->method('postUpdate')->with($this->api, $object);

        $this->api->addExtension($extension);

        $this->api->update($object);

        self::assertEquals($object, $this->api->getSubject());
        self::assertEquals(['preUpdate', 'postUpdate'], $this->api->calledEvents);
    }

    public function testCreate()
    {
        $object = new \stdClass();

        $this->container->expects(self::any())
                        ->method('get')
                        ->with('doctrine')
                        ->willReturn($this->doctrine);

        $this->manager->expects(self::once())->method('persist')->with($object);
        $this->manager->expects(self::once())->method('flush')->with($object);
        $this->manager->expects(self::once())->method('refresh')->with($object);

        $extension = self::createMock(ApiExtensionInterface::class);
        $extension->expects(self::once())->method('prePersist')->with($this->api, $object);
        $extension->expects(self::once())->method('postPersist')->with($this->api, $object);

        $this->api->addExtension($extension);

        $this->api->create($object);

        self::assertEquals($object, $this->api->getSubject());
        self::assertEquals(['prePersist', 'postPersist'], $this->api->calledEvents);
    }

    public function testRemove()
    {
        $object = new \stdClass();

        $this->container->expects(self::any())
                        ->method('get')
                        ->with('doctrine')
                        ->willReturn($this->doctrine);

        $this->manager->expects(self::once())->method('remove')->with($object);
        $this->manager->expects(self::once())->method('flush')->with($object);

        $extension = self::createMock(ApiExtensionInterface::class);
        $extension->expects(self::once())->method('preRemove')->with($this->api, $object);
        $extension->expects(self::once())->method('postRemove')->with($this->api, $object);

        $this->api->addExtension($extension);

        $this->api->remove($object);

        self::assertEquals($object, $this->api->getSubject());
        self::assertEquals(['preRemove', 'postRemove'], $this->api->calledEvents);
    }

    public function testValidate()
    {
        $object = new \stdClass();
        $violations = new ConstraintViolationList();

        $validator = self::createMock(ValidatorInterface::class);
        $validator->expects(self::once())->method('validate')->with($object)->willReturn($violations);

        $this->container->expects(self::any())
                        ->method('get')
                        ->with('validator')
                        ->willReturn($validator);

        self::assertEquals($violations, $this->api->validate($object));
        self::assertEquals(['preValidate'], $this->api->calledEvents);
    }

    public function testGetUser_WithoutSecurityBundle()
    {
        $this->container->expects(self::any())
                        ->method('has')
                        ->with('security.token_storage')
                        ->willReturn(false);
        self::expectException(\LogicException::class);
        self::expectExceptionMessage('The SecurityBundle is not registered in your application.');

        $this->api->getUser();
    }

    public function testGetUser_WithoutValidToken()
    {
        $this->container->expects(self::any())
                        ->method('has')
                        ->with('security.token_storage')
                        ->willReturn(true);

        $tokenStorage = self::createMock(TokenStorage::class);
        $tokenStorage->expects(self::once())->method('getToken')->willReturn(null);

        $this->container->expects(self::any())
                        ->method('get')
                        ->with('security.token_storage')
                        ->willReturn($tokenStorage);

        self::assertNull($this->api->getUser());
    }

    public function testGetUser_WithoutValidUserInToken()
    {
        $this->container->expects(self::any())
                        ->method('has')
                        ->with('security.token_storage')
                        ->willReturn(true);

        $tokenStorage = self::createMock(TokenStorage::class);
        $token = self::createMock(TokenInterface::class);

        $tokenStorage->expects(self::once())->method('getToken')->willReturn($token);
        $token->expects(self::once())->method('getUser')->willReturn(null);

        $this->container->expects(self::any())
                        ->method('get')
                        ->with('security.token_storage')
                        ->willReturn($tokenStorage);

        self::assertNull($this->api->getUser());
    }

    public function testGetUser()
    {
        $user = new User();
        $this->container->expects(self::any())
                        ->method('has')
                        ->with('security.token_storage')
                        ->willReturn(true);

        $tokenStorage = self::createMock(TokenStorage::class);
        $token = self::createMock(TokenInterface::class);

        $tokenStorage->expects(self::once())->method('getToken')->willReturn($token);
        $token->expects(self::once())->method('getUser')->willReturn($user);

        $this->container->expects(self::any())
                        ->method('get')
                        ->with('security.token_storage')
                        ->willReturn($tokenStorage);

        self::assertEquals($user, $this->api->getUser());
    }

    public function testLoadSubjectFromRequest_RequestBodyContent()
    {
        $object = new User();

        $context = DeserializationContext::create();
        $context->setGroups(['public']);

        $request = self::createMock(Request::class);

        $request->expects(self::once())->method('getContent')->willReturn('{"username":"admin"}');
        $request->expects(self::any())->method('get')
                ->withConsecutive(['_route'], ['userId'])
                ->willReturnOnConsecutiveCalls('user_list', 1);

        $specification = self::createMock(RestApiSpecification::class);
        $specification->expects(self::once())
                      ->method('getRequestBodyClassAndGroups')
                      ->with('user_list')
                      ->willReturn([User::class, ['public']]);

        $serializer = self::createMock(SerializerInterface::class);
        $serializer->expects(self::once())
                   ->method('deserialize')
                   ->with('{"username":"admin","id":1}', User::class, 'json', $context)
                   ->willReturn($object);

        $this->container->expects(self::any())
                        ->method('get')
                        ->withConsecutive(['restful_platform.api_specification'], ['serializer'])
                        ->willReturnOnConsecutiveCalls($specification, $serializer);

        $extension = self::createMock(ApiExtensionInterface::class);
        $extension->expects(self::once())->method('alterObject')->with($this->api, $object);
        $this->api->addExtension($extension);

        $this->api->setRequest($request);

        self::assertEquals($object, $this->api->getSubject());
    }

    public function testLoadSubjectFromRequest_SerializerException()
    {
        $object = new User();

        $context = DeserializationContext::create();
        $context->setGroups(['public']);

        $request = self::createMock(Request::class);

        $request->expects(self::once())->method('getContent')->willReturn('{"username":"admin"}');
        $request->expects(self::any())->method('get')
                ->withConsecutive(['_route'], ['userId'])
                ->willReturnOnConsecutiveCalls('user_list', 1);

        $specification = self::createMock(RestApiSpecification::class);
        $specification->expects(self::once())
                      ->method('getRequestBodyClassAndGroups')
                      ->with('user_list')
                      ->willReturn([User::class, ['public']]);

        $serializer = self::createMock(SerializerInterface::class);
        $serializer->expects(self::once())
                   ->method('deserialize')
                   ->with('{"username":"admin","id":1}', User::class, 'json', $context)
                   ->willThrowException(new \Exception('Json syntax error'));

        $logger = self::createMock(LoggerInterface::class);
        $logger->expects(self::once())->method('error')->with('Json syntax error');

        $this->container->expects(self::any())
                        ->method('has')
                        ->withConsecutive(['logger'])
                        ->willReturnOnConsecutiveCalls(true);

        $this->container->expects(self::any())
                        ->method('get')
                        ->withConsecutive(['restful_platform.api_specification'], ['serializer'], ['logger'])
                        ->willReturnOnConsecutiveCalls($specification, $serializer, $logger);

        self::expectException(ApiError::class);
        self::expectExceptionMessage('Json syntax error');
        $this->api->setRequest($request);

        self::assertNull($this->api->getSubject());
    }

    public function testLoadSubjectFromRequest_UsingPathParameter()
    {
        $object = new User();
        $context = DeserializationContext::create();
        $context->setGroups(['public']);

        $request = self::createMock(Request::class);

        $request->expects(self::once())->method('getContent')->willReturn(null);
        $request->expects(self::any())->method('get')
                ->withConsecutive(['userId'])
                ->willReturnOnConsecutiveCalls( 1);

        $this->container->expects(self::any())->method('get')->with('doctrine')->willReturn($this->doctrine);
        $this->repository->method('findOneBy')->with(['id' => 1])->willReturn($object);

        $this->api->setRequest($request);

        self::assertEquals($object, $this->api->getResource(1));
    }
}
