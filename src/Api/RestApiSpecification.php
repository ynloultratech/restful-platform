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

use Doctrine\Common\Inflector\Inflector;
use JMS\Serializer\Expression\ExpressionEvaluator;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerBuilder;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Ynlo\RestfulPlatformBundle\Swagger\Model\Operation;
use Ynlo\RestfulPlatformBundle\Swagger\Model\Schema;
use Ynlo\RestfulPlatformBundle\Swagger\Model\SwaggerObject;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Common\SpecDecorator;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema\Model\Describer\DocCommentDescriber;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema\Model\Describer\DoctrineDescriber;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema\Model\Describer\ExampleDescriber;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema\Model\Describer\JMSSerializerDescriber;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema\Model\Describer\SwaggerAnnotationsDescriber;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema\Model\Describer\SymfonyValidatorDescriber;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema\ModelSpec;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\SWObject;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\SWOperation;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\SWPath;

class RestApiSpecification
{
    /**
     * @var RestApiPool
     */
    protected $apiPool;

    /**
     * @var array
     */
    protected $config;

    /**
     * @var SwaggerObject
     */
    protected $specification;

    /**
     * Map API operation id with specific route routeName
     *
     * @var array
     */
    protected $actionMap = [];

    /**
     * @var string
     */
    protected $cacheDir;

    /**
     * RestApiSpecification constructor.
     *
     * @param RestApiPool $apiPool
     * @param array       $config
     * @param string      $cacheDir
     */
    public function __construct(RestApiPool $apiPool, $config = [], $cacheDir)
    {
        $this->apiPool = $apiPool;
        $this->config = $config;
        $this->cacheDir = $cacheDir;

        ModelSpec::addDescriber(new SwaggerAnnotationsDescriber());
        ModelSpec::addDescriber(new JMSSerializerDescriber());
        ModelSpec::addDescriber(new DoctrineDescriber());
        ModelSpec::addDescriber(new SymfonyValidatorDescriber());
        ModelSpec::addDescriber(new DocCommentDescriber());
        ModelSpec::addDescriber(new ExampleDescriber());

        $this->initialize();
    }

    /**
     * @param string $format desired output format, json or yaml
     *
     * @return string
     */
    public function serialize($format = 'json')
    {
        $serializer = SerializerBuilder::create()
                                       ->setExpressionEvaluator(new ExpressionEvaluator(new ExpressionLanguage()))
                                       ->build();

        $context = SerializationContext::create()->setSerializeNull(false);

        return $serializer->serialize($this->specification, $format, $context);
    }

    /**
     * @return SwaggerObject
     */
    public function getSpecification()
    {
        return $this->specification;
    }

    protected function initialize()
    {
        $this->loadCache();
        if ($this->specification) {
            return;
        }

        $paths = [];
        foreach ($this->apiPool->getElements() as $api) {
            $routes = $api->getRoutes()->getElements();
            foreach ($routes as $name => $route) {
                $operationSpecs = [];

                if ($route->getDefault('_api') && strpos($route->getDefault('_api'), ':') !== false) {
                    list($class, $action) = explode(':', $route->getDefault('_api'));
                    $operationSpecs = $api->$action();

                    //use operation id like merchant_get or userRole_list to allow
                    //remove prefix using the --remove-operation-id-prefix in the swagger codegen
                    $operationId = Inflector::camelize($api->getBaseRouteName()).'_'.preg_replace('/Operation$/', '', Inflector::camelize($action));
                    $operationSpecs[] = SWOperation::operationId($operationId);
                    $operationSpecs[] = SWOperation::tag($api->getLabel());
                    $this->actionMap[$name] = $operationId;
                }

                $method = strtolower($route->getMethods()[0]);
                $operations = [
                    SWPath::$method($operationSpecs),
                ];
                $paths[] = SWObject::path($route->getPath(), $operations);
            }
        }

        $title = $this->config['documentation']['info']['title'] ?? 'API';
        $description = $this->config['documentation']['info']['description'] ?? '';
        $version = $this->config['documentation']['info']['version'] ?? '';

        $specs = [
            SWObject::info($title, $description, $version),
            SWObject::host($this->config['host'] ?? ''),
            SWObject::basePath($this->config['base_path'] ?? ''),
        ];

        $specs = array_merge($specs, $paths);

        $this->specification = new SwaggerObject();
        foreach ($specs as $spec) {
            if ($spec instanceof SpecDecorator) {
                $decorator = $spec->getDecorator();
                $decorator($this->specification);
            }
        }

        $tags = $this->config['documentation']['tags']??[];

        //Root is the first by default
        if (!isset($tags['Root'])) {
            $this->specification->addTag('Root', 'Root Endpoint to discover all possible API operations');
        }

        if ($tags) {
            foreach ($tags as $name => $tag) {
                $description = null;
                if (is_string($tag) && $tag) {
                    $description = $tag;
                } elseif (is_array($tag) && isset($tag['description'])) {
                    $description = $tag['description'];
                }
                $this->specification->addTag($name, $description);
            }
        }

        $this->saveCache();
    }

    /**
     * @param string $routeName
     *
     * @return Operation
     */
    public function getOperation($routeName)
    {
        $operationId = $this->getOperationId($routeName);
        if ($operationId) {
            foreach ($this->specification->getPaths() as $path) {
                foreach ($path->getOperations() as $operation) {
                    if ($operation->getOperationId() === $operationId) {
                        return $operation;
                        break;
                    }
                }
            }
        }

        return null;
    }

    /**
     * @param string $routeName
     *
     * @return string
     */
    public function getOperationId($routeName)
    {
        if (isset($this->actionMap[$routeName])) {
            return $this->actionMap[$routeName];
        }

        return null;
    }

    /**
     * @param $routeName
     *
     * @return array
     */
    public function getRequestBodyClassAndGroups($routeName)
    {
        $operation = $this->getOperation($routeName);
        if ($operation) {
            foreach ($operation->getParameters() as $parameter) {
                if ($parameter->getIn() === 'body' && $schema = $parameter->getSchema()) {
                    if ($schema instanceof Schema && $schema->getClass()) {
                        return [$schema->getClass(), $schema->getGroups()];
                    }
                }
            }
        }

        return [null, []];
    }

    /**
     * @param $routeName
     * @param $responseCode
     *
     * @return array
     */
    public function getResponseGroups($routeName, $responseCode)
    {
        $operation = $this->getOperation($routeName);
        if ($operation && $schema = $operation->getResponse($responseCode)->getSchema()) {
            return $schema->getGroups();
        }

        return [];
    }

    /**
     * remove the specification cache
     */
    public function clearCache()
    {
        @unlink($this->cacheFileName());
        $this->initialize();
    }

    protected function cacheFileName()
    {
        return $this->cacheDir.'/api_spec.meta';
    }

    protected function loadCache()
    {
        if (file_exists($this->cacheFileName())) {
            $content = @file_get_contents($this->cacheFileName());
            if ($content) {
                list($this->actionMap, $this->specification) = unserialize($content);
            }
        }
    }

    protected function saveCache()
    {
        file_put_contents($this->cacheFileName(), serialize([$this->actionMap, $this->getSpecification()]));
    }
}