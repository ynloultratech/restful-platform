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

namespace Ynlo\RestfulPlatformBundle\Util;

use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation\AccessorOrder;
use JMS\Serializer\Annotation\Exclude;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Annotation\VirtualProperty;
use JMS\Serializer\Metadata\PropertyMetadata;
use JMS\Serializer\Metadata\VirtualPropertyMetadata;
use JMS\Serializer\Naming\PropertyNamingStrategyInterface;
use Symfony\Component\PropertyInfo\Type as PropertyType;

/**
 * This reader helps to read some properties or classes to know how will be serialized
 */
class SerializerReader
{
    /**
     * @var PropertyNamingStrategyInterface
     */
    public static $namingStrategy;

    /**
     * @param \ReflectionClass|$class |$object $class
     * @param array            $groups
     *
     * @return array
     */
    public static function getProperties(\ReflectionClass $class, $groups = [])
    {
        if (is_string($class)) {
            $class = new \ReflectionClass($class);
        } elseif (is_object($class) && !($class instanceof \ReflectionClass)) {
            $class = new \ReflectionClass(get_class($class));
        }

        $includedByDefault = true;
        /** @var ExclusionPolicy $exclusionPolicy */
        if ($exclusionPolicy = AnnotationReader::getAnnotationFor($class, ExclusionPolicy::class)) {
            if (strtolower($exclusionPolicy->policy) === 'all') {
                $includedByDefault = false;
            }
        }

        $properties = [];
        foreach ($class->getProperties() as $property) {
            if ($includedByDefault) {
                if (!AnnotationReader::getAnnotationFor($property, Exclude::class)) {
                    $properties[$property->getName()] = $property;
                }
            } else {
                if (AnnotationReader::getAnnotationFor($property, Expose::class)) {
                    $properties[$property->getName()] = $property;
                }
            }
        }

        foreach ($class->getMethods() as $property) {
            if ($includedByDefault) {
                if (!AnnotationReader::getAnnotationFor($property, Exclude::class)) {
                    if (AnnotationReader::getAnnotationFor($property, VirtualProperty::class)) {
                        $properties[$property->getName()] = $property;
                    }
                }
            } else {
                if (AnnotationReader::getAnnotationFor($property, VirtualProperty::class)) {
                    $properties[$property->getName()] = $property;
                }
            }
        }

        if ($annotations = AnnotationReader::getAnnotationsFor($class)) {
            foreach ($annotations as $annotation) {
                if ($annotation instanceof VirtualProperty) {
                    $metaData = new VirtualPropertyMetadata($class->name, $annotation->name);
                    $name = self::$namingStrategy ? self::$namingStrategy->translateName($metaData) : $metaData->name;
                    if ($annotation->options) {
                        foreach ($annotation->options as $option) {
                            if ($option instanceof SerializedName) {
                                $name = $option->name;
                            }
                            if ($option instanceof Type) {
                                $metaData->type = $option->name;
                            }
                            if ($option instanceof Groups) {
                                $metaData->groups = $option->groups;
                            }
                        }
                    }
                    $metaData->serializedName = $name;
                    $properties[$name] = $metaData;
                }
            }
        }

        if ($groups) {
            foreach ($properties as $name => $property) {
                $propertyGroups = [];
                if ($property instanceof VirtualPropertyMetadata) {
                    $propertyGroups = $property->groups;
                } else {
                    /** @var Groups $propertyGroupsAnnotation */
                    if ($propertyGroupsAnnotation = AnnotationReader::getAnnotationFor($property, Groups::class)) {
                        $propertyGroups = $propertyGroupsAnnotation->groups;
                    }
                }

                if ($propertyGroups) {
                    if (!array_intersect($groups, $propertyGroups)) {
                        unset($properties[$name]);
                    }
                } elseif ($groups && !in_array('Default', $groups)) {
                    unset($properties[$name]);
                }
            }
        }

        /** @var AccessorOrder $order */
        if ($order = AnnotationReader::getAnnotationFor($class, AccessorOrder::class)) {
            if ($order->order === 'alphabetical') {
                ksort($properties);
            } elseif ($order->custom) {
                $custom = array_flip($order->custom);
                $properties = array_merge($custom, $properties);
                $properties = array_filter(
                    $properties,
                    function ($property) {
                        return (($property instanceof \ReflectionProperty)
                                || ($property instanceof \ReflectionMethod)
                                || ($property instanceof VirtualPropertyMetadata)
                        );
                    }
                );
            }
        }

        return $properties;
    }

    /**
     * Get the final serialized name of some property
     *
     * @param \ReflectionProperty|\ReflectionMethod $property
     *
     * @return string
     */
    public static function getSerializedName($property)
    {
        if (!self::$namingStrategy) {
            return $property->getName();
        }

        /** @var SerializedName $serializedName */
        $serializedName = AnnotationReader::getAnnotationFor($property, SerializedName::class);
        if ($serializedName) {
            return $serializedName->name;
        }

        if ($property instanceof \ReflectionProperty) {
            $metaData = new PropertyMetadata($property->class, $property->name);
        } else {
            $metaData = new VirtualPropertyMetadata($property->class, $property->name);
        }

        return self::$namingStrategy->translateName($metaData);
    }

    /**
     * @param \ReflectionProperty|\ReflectionMethod $property
     *
     * @return PropertyType
     */
    public static function getType($property)
    {
        $builtInType = 'string';
        $class = null;
        $collection = false;
        $itemType = null;
        $keyType = null;

        $serializerType = AnnotationReader::getAnnotationFor($property, Type::class);
        if ($serializerType) {
            $jmsType = $serializerType->name;

            //Support for array<T> and array<K,T>
            if (preg_match('/^(array)|^(ArrayCollection)/', $jmsType)) {
                $collection = true;
                $builtInType = 'array';
                if (preg_match('/^[ArrayCollection]/', $jmsType)) {
                    $builtInType = 'object';
                    $class = ArrayCollection::class;
                }

                if (preg_match('/<\'?([^>]+)\'?>$/', $jmsType, $matches)) {
                    $itemType = preg_replace('/\s+/', '', $matches[1]);
                    if (strpos($itemType, ',') !== false) {
                        list($keyType, $itemType) = explode(',', $itemType);
                    }
                }
            } elseif ($jmsType && class_exists($jmsType)) {
                $class = $jmsType;
            } else {
                $builtInType = $jmsType;
            }
        }

        if ($itemType && class_exists($itemType)) {
            $itemType = new PropertyType('object', true, $itemType);
        } else {
            $itemType = new PropertyType($itemType, true);
        }

        $keyType = new PropertyType($keyType);

        return new PropertyType($builtInType, true, $class, $collection, $keyType, $itemType);
    }

    /**
     * Get a serializer property path form given symfony property path.
     * Can me used to share a property path with the final user.
     *
     * @param string|object|\ReflectionClass $root
     * @param string                         $path
     *
     * @return string
     */
    public static function getSerializedPropertyPath($root, $path)
    {
        if (is_object($root)) {
            $root = get_class($root);
        }

        if (strpos($path, '.') !== false) {
            $pathArray = explode('.', $path);
        } else {
            $pathArray = [$path];
        }

        $contextClass = $root;
        foreach ($pathArray as &$propName) {
            $index = null;
            if (preg_match('/(\w+)(\[\d+\])$/', $propName, $matches)) {
                $propName = $matches[1];
                $index = $matches[2];
            }

            $refProperty = new \ReflectionProperty($contextClass, $propName);
            $propName = self::getSerializedName($refProperty).$index;

            $type = self::getType($refProperty);
            if (in_array($type->getBuiltinType(), ['object', 'array'])) {
                if ($type->isCollection()) {
                    $contextClass = $type->getCollectionValueType()->getClassName();
                    if (!$contextClass) {
                        break;
                    }
                } else {
                    $contextClass = $type->getClassName();
                }
            } else {
                break;
            }
        }
        unset($propName);

        return implode('.', $pathArray);
    }
}