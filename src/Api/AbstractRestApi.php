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

use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\Common\Util\Inflector;
use Doctrine\ORM\EntityRepository;
use JMS\Serializer\DeserializationContext;
use JMS\Serializer\Exception\LogicException;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Ynlo\RestfulPlatformBundle\Api\Extension\ApiExtensionInterface;
use Ynlo\RestfulPlatformBundle\Controller\RestApiController;
use Ynlo\RestfulPlatformBundle\Exception\ApiError;
use Ynlo\RestfulPlatformBundle\Routing\ApiRouteCollection;

abstract class AbstractRestApi implements RestApiInterface
{
    use ContainerAwareTrait;

    /**
     * Resource to manage
     *
     * @var string
     */
    protected $resourceClass;

    /**
     * The label to represent this api and all operations.
     * Used in the swagger documentation as tag.
     *
     * e.g.:  `User` or `Company`
     *
     * @var string
     */
    protected $label;

    /**
     * The base name controller is used as default controller for all actions
     *
     * @var string
     */
    protected $baseControllerName = RestApiController::class;

    /**
     * The base route name used to generate the routing information.
     *
     * e.g.
     *
     * 'user' generate route names like 'user_list' or 'user_create'
     *
     * @var string
     */
    protected $baseRouteName;

    /**
     * The base route pattern used to generate the routing information.
     *
     * e.g.
     *
     * '/users' generate routes like [GET]/users or [GET]/users/{userId}
     *
     * @var string
     */
    protected $baseRoutePattern;

    /**
     * Name of the parameter to retrieve the resource identifier from request
     *
     * e.g.
     * 'id' or 'userId' and the parameter is requested using $request->get('id')
     *
     * This parameter name is exposed in the documentation
     *
     * @var string
     */
    protected $idParameter;

    /**
     * Name of the field in the DB to retrieve the resource,
     * commonly is the `id` but can use other to use `slugs`
     *
     * @var string
     */
    protected $idField = 'id';

    /**
     * @var object
     */
    protected $subject;

    /**
     * Use pagination in the
     *
     * @var bool
     */
    protected $pagination = true;

    /**
     * @var int
     */
    protected $maxResults = 30;

    /**
     * @var ApiRouteCollection
     */
    protected $routes;

    /**
     * Current request
     *
     * @var Request
     */
    protected $request;

    /**
     * @var ApiExtensionInterface[]
     */
    protected $extensions = [];

    /**
     * AbstractRestApi constructor.
     */
    public function __construct()
    {
        if (!$this->label) {
            if ($this->resourceClass) {
                preg_match('/\w+$/', $this->resourceClass, $matches);
                $this->label = $matches[0];
            } else {
                $msg = 'Must provide a `resourceClass` or `label` to the API class `%s`';
                throw new LogicException(sprintf($msg, get_class($this)));
            }
        }

        if (!$this->baseRouteName) {
            $this->baseRouteName = Inflector::singularize(Inflector::tableize($this->label));
        }

        if (!$this->baseRoutePattern) {
            $this->baseRoutePattern = '/'.Inflector::pluralize(Inflector::tableize($this->label));
        }

        if (!$this->idParameter) {
            $resourceName = Inflector::singularize(Inflector::tableize($this->label));
            $this->idParameter = Inflector::camelize($resourceName.'_'.$this->idField);
        }

        //convert common class names to symfony controller notation
        if ($this->baseControllerName && class_exists($this->baseControllerName)) {
            preg_match('/\w+Bundle/', $this->baseControllerName, $matches);
            if (isset($matches[0])) {
                $bundle = $matches[0];
                preg_match('/(\w+)Controller/', $this->baseControllerName, $matches);
                if (isset($matches[1])) {
                    $controller = $matches[1];
                    $this->baseControllerName = "$bundle:$controller";
                }
            }
        }
    }

    /**
     * @return string
     */
    public function getResourceClass()
    {
        return $this->resourceClass;
    }

    /**
     * @param string $resourceClass
     *
     * @return AbstractRestApi
     */
    public function setResourceClass(string $resourceClass): AbstractRestApi
    {
        $this->resourceClass = $resourceClass;

        return $this;
    }

    /**
     * @return string
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * @param string $label
     *
     * @return AbstractRestApi
     */
    public function setLabel(string $label): AbstractRestApi
    {
        $this->label = $label;

        return $this;
    }

    /**
     * @return string
     */
    public function getBaseControllerName(): string
    {
        return $this->baseControllerName;
    }

    /**
     * @param string $baseControllerName
     *
     * @return AbstractRestApi
     */
    public function setBaseControllerName(string $baseControllerName): AbstractRestApi
    {
        $this->baseControllerName = $baseControllerName;

        return $this;
    }

    /**
     * @return string
     */
    public function getBaseRouteName(): string
    {
        return $this->baseRouteName;
    }

    /**
     * @param string $baseRouteName
     *
     * @return AbstractRestApi
     */
    public function setBaseRouteName(string $baseRouteName): AbstractRestApi
    {
        $this->baseRouteName = $baseRouteName;

        return $this;
    }

    /**
     * @return string
     */
    public function getBaseRoutePattern(): string
    {
        return $this->baseRoutePattern;
    }

    /**
     * @param string $baseRoutePattern
     *
     * @return AbstractRestApi
     */
    public function setBaseRoutePattern(string $baseRoutePattern): AbstractRestApi
    {
        $this->baseRoutePattern = $baseRoutePattern;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getIdParameter()
    {
        return $this->idParameter;
    }

    /**
     * @inheritDoc
     */
    public function getRouterIdParameter()
    {
        return "{{$this->idParameter}}";
    }

    /**
     * @inheritDoc
     */
    public function getIdField()
    {
        return $this->idField;
    }

    /**
     * @return object
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @param object $subject
     *
     * @return AbstractRestApi
     */
    public function setSubject($subject): AbstractRestApi
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getResource($identifier)
    {
        return $this->getRepository()->findOneBy([$this->getIdField() => $identifier]);
    }

    /**
     * @inheritDoc
     */
    public function setRequest(Request $request): AbstractRestApi
    {
        $this->request = $request;
        $this->loadSubjectFromRequest($request);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getRequest()
    {
        if (!$this->request) {
            $this->request = $this->container->get('request_stack')->getCurrentRequest();
        }

        if (!$this->request) {
            $this->request = Request::createFromGlobals();
        }

        return $this->request;
    }

    /**
     * @inheritDoc
     */
    public function hasRoute($name)
    {
        return $this->getRoutes()->has($name);
    }

    /**
     * @inheritDoc
     */
    public function generateUrl($name, $parameters = [], $absolute = UrlGeneratorInterface::ABSOLUTE_PATH)
    {
        //TODO: get from config
        $version = $this->getRequest()->get('version', 'v1');
        $parameters = array_merge(['version' => $version], $parameters);

        $routeName = $this->getRoutes()->getRouteName($name);

        return $this->container->get('router')->generate($routeName, $parameters, $absolute);
    }

    /**
     * @inheritDoc
     */
    public function generateResourceUrl($name, $resource, $parameters = [], $absolute = UrlGeneratorInterface::ABSOLUTE_PATH)
    {
        // TODO: Implement generateResourceUrl() method.
    }

    /**
     * @inheritDoc
     */
    public function getRoutes()
    {
        if ($this->routes) {
            return $this->routes;
        }

        $this->routes = new ApiRouteCollection($this);

        $this->configureRoutes($this->routes);

        return $this->routes;
    }

    /**
     * @inheritDoc
     */
    public function getManager()
    {
        return $this->container->get('doctrine')->getManager();
    }

    /**
     * @inheritDoc
     */
    public function createQuery()
    {
        $repo = $this->getRepository();
        if ($repo instanceof EntityRepository) {
            $query = $repo->createQueryBuilder('o');
            foreach ($this->getExtensions() as $extension) {
                $extension->configureQuery($this, $query);
            }

            return $query;
        }

        throw new \LogicException(sprintf('The repository %s if not a valid EntityRepository', get_class($repo)));
    }

    /**
     * @inheritDoc
     */
    public function update($object)
    {
        $this->subject = $object;
        foreach ($this->getExtensions() as $extension) {
            $extension->preUpdate($this, $object);
        }
        $this->preUpdate($object);
        $this->getManager()->flush($object);
        $this->getManager()->refresh($object);
        foreach ($this->getExtensions() as $extension) {
            $extension->postUpdate($this, $object);
        }
        $this->postUpdate($object);
    }

    /**
     * @inheritDoc
     */
    public function create($object)
    {
        $this->subject = $object;
        foreach ($this->getExtensions() as $extension) {
            $extension->prePersist($this, $object);
        }
        $this->prePersist($object);
        $this->getManager()->persist($object);
        $this->getManager()->flush($object);
        $this->getManager()->refresh($object);
        foreach ($this->getExtensions() as $extension) {
            $extension->postPersist($this, $object);
        }
        $this->postPersist($object);
    }

    /**
     * @inheritDoc
     */
    public function remove($object)
    {
        $this->subject = $object;
        foreach ($this->getExtensions() as $extension) {
            $extension->preRemove($this, $object);
        }
        $this->preRemove($object);
        $this->getManager()->remove($object);
        $this->getManager()->flush($object);
        foreach ($this->getExtensions() as $extension) {
            $extension->postRemove($this, $object);
        }
        $this->postRemove($object);
    }

    /**
     * @inheritDoc
     */
    public function validate($object)
    {
        $this->preValidate($object);

        return $this->container->get('validator')->validate($object);
    }

    /**
     * @inheritDoc
     */
    public function preValidate($object)
    {

    }

    /**
     * @inheritDoc
     */
    public function preUpdate($object)
    {

    }

    /**
     * @inheritDoc
     */
    public function postUpdate($object)
    {

    }

    /**
     * @inheritDoc
     */
    public function prePersist($object)
    {

    }

    /**
     * @inheritDoc
     */
    public function postPersist($object)
    {

    }

    /**
     * @inheritDoc
     */
    public function preRemove($object)
    {

    }

    /**
     * @inheritDoc
     */
    public function postRemove($object)
    {

    }

    /**
     * Get a user from the Security Token Storage.
     *
     * @return UserInterface|null
     */
    public function getUser()
    {
        if (!$this->container->has('security.token_storage')) {
            throw new \LogicException('The SecurityBundle is not registered in your application.');
        }

        /** @var TokenInterface $token */
        if (null === $token = $this->container->get('security.token_storage')->getToken()) {
            return null;
        }

        if (!is_object($user = $token->getUser())) {
            // e.g. anonymous authentication
            return null;
        }

        return $user;
    }

    /**
     * @param Request $request
     */
    protected function loadSubjectFromRequest(Request $request)
    {
        if (($data = $request->getContent()) && is_string($data)) {
            $routeName = $request->get('_route');
            list($class, $groups) = $this->getApiSpecification()->getRequestBodyClassAndGroups($routeName);
            if ($class) {
                //if the `id` is not present in the body, given in url: /company/1
                //the serializer doctrineConstructor can`t find the object
                //to avoid this, manually set the id in the json before deserialize
                if ($id = $request->get($this->getIdParameter())) {
                    $decodedBody = json_decode($data, true);
                    if (!isset($decodedBody[$this->getIdField()])) {
                        $decodedBody[$this->getIdField()] = $id;
                        $data = json_encode($decodedBody);
                    }
                }

                $context = DeserializationContext::create();
                if ($groups) {
                    $context->setGroups($groups);
                }
                try {
                    $this->subject = $this->deserialize($data, $class, 'json', $context);
                } catch (\Exception $exception) {
                    if ($this->container->has('logger')) {
                        $this->container->get('logger')->error($exception->getMessage(), $exception->getTrace());
                    }

                    if (strpos($exception->getMessage(), 'syntax error')) {
                        throw ApiError::badRequest(400, $exception->getMessage());
                    }
                }
            }
        }

        if (!$this->subject && $id = $request->get($this->getIdParameter())) {
            $this->subject = $this->getResource($id);
        }

        if ($this->subject) {
            foreach ($this->getExtensions() as $extension) {
                $extension->alterObject($this, $this->subject);
            }
        }
    }

    protected function deserialize($data, $type, $format = 'json', DeserializationContext $context = null)
    {
        return $this->container->get('serializer')->deserialize($data, $type, $format, $context);
    }

    /**
     * @return ObjectRepository|EntityRepository
     */
    protected function getRepository()
    {
        return $this->getManager()->getRepository($this->getResourceClass());
    }

    /**
     * @return object|RestApiSpecification
     */
    protected function getApiSpecification()
    {
        return $this->container->get('restful_platform.api_specification');
    }

    /**
     * @inheritDoc
     */
    public function getExtensions()
    {
        return $this->extensions;
    }

    /**
     * @inheritDoc
     */
    public function addExtension(ApiExtensionInterface $extension)
    {
        if (!in_array($extension, $this->getExtensions())) {
            $this->extensions[] = $extension;
        }

        return $this;
    }

    /**
     * @param ApiRouteCollection $routes
     */
    abstract protected function configureRoutes(ApiRouteCollection $routes);
}