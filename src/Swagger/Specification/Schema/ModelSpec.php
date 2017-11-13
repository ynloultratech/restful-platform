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

namespace Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema;

use Ynlo\RestfulPlatformBundle\Util\SerializerReader;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Common\SpecDecorator;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema\Model\DescribeContext;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema\Model\ModelDescriberInterface;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema\Model\ModelPropertySchema;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\SWSchema;

class ModelSpec implements SpecDecorator
{
    protected $class;

    protected $groups = [];

    protected $deep;

    /**
     * @var array|ModelDescriberInterface[]
     */
    protected static $modelDescribers = [];

    /**
     * @param string  $class
     * @param array   $groups
     * @param integer $deep
     */
    public function __construct($class, array $groups = [], int $deep = 3)
    {
        $this->class = $class;
        $this->groups = $groups;
        $this->deep = $deep;
    }

    /**
     * @param ModelDescriberInterface $describer
     */
    public static function addDescriber(ModelDescriberInterface $describer)
    {
        self::$modelDescribers[] = $describer;
    }

    /**
     * @param ModelDescriberInterface[]|array $describers
     */
    public static function setDescribers(array $describers)
    {
        self::$modelDescribers = $describers;
    }

    /**
     * {@inheritdoc}
     */
    public function getDecorator(): callable
    {
        $schemaName = null;

        $refClass = new \ReflectionClass($this->class);

        $props = SerializerReader::getProperties($refClass, $this->groups);

        $propsSpecs = [];

        if (!$schemaName) {
            //resolve schema name based on className
            preg_match('/\w+$/', $this->class, $matches);
            if (isset($matches[0])) {
                $schemaName = $matches[0];
            }
        }

        /**
         * @var string                                $name
         * @var \ReflectionMethod|\ReflectionProperty $prop
         */
        foreach ($props as $name => $prop) {
            $modelProp = new ModelPropertySchema($name);
            foreach (self::$modelDescribers as $describer) {
                $context = new DescribeContext($prop, $this->groups);
                if ($describer->supports($modelProp, $context)) {
                    $describer->describe($modelProp, $context);
                }
            }

            $addSpecs = [];
            $addSpecs[] = SWSchema::description($modelProp->getDescription());
            $addSpecs[] = SWSchema::example($modelProp->getExample());
            $addSpecs[] = SWSchema::enum($modelProp->getEnum());
            $addSpecs[] = SWSchema::groups($modelProp->getGroups());

            //simple schema inside property, child object
            if (class_exists($modelProp->getType()) && preg_match('/\w+\\\/', $modelProp->getType())) {
                if (!$this->deep) {
                    continue;
                }

                $model = SWSchema::model($modelProp->getType(), $this->groups, $this->deep - 1);
                $propsSpecs[] = SWSchema::property($modelProp->getName(), $model, null, [$addSpecs]);
                $propsSpecs[] = SWSchema::type('object');
            } else {
                //array of items
                if ($modelProp->getType() === 'array') {
                    $itemType = $modelProp->getItemType();

                    $model = null;
                    if (class_exists($itemType)) {
                        if (!$this->deep) {
                            continue;
                        }
                        $model = SWSchema::model($itemType, $this->groups, $this->deep - 1);
                    }

                    //for indexed array of items e.g. "key" => $value
                    if ($modelProp->getKeyType()) {
                        if ($model !== null) { //when value is a object e.g. "key" => $user
                            $addSpecs[] = SWSchema::additionalProperties($model);
                        } else {
                            //when value is a scalar type e.g. "key" => 'userName'
                            $addSpecs[] = SWSchema::additionalProperties($itemType);
                        }
                    } else { //for non indexed array of items
                        if ($model !== null) {
                            $addSpecs[] = SWSchema::items($model);
                        } else {
                            $addSpecs[] = SWSchema::items($itemType);
                        }
                    }
                }

                $propsSpecs[] = SWSchema::property(
                    $modelProp->getName(),
                    $modelProp->getType(),
                    $modelProp->getFormat(),
                    $addSpecs
                );
            }
        }

        return function ($spec) use ($schemaName, $propsSpecs) {
            $propsSpecs[] = SWSchema::mappedClass($this->class);
            if ($this->groups) {
                $propsSpecs[] = SWSchema::groups($this->groups);
            }
            $schema = SWSchema::schema($schemaName, $propsSpecs);
            $decorator = $schema->getDecorator();
            $decorator($spec);
        };
    }
}