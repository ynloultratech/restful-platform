<?php
/**
 * LICENSE: This file is subject to the terms and conditions defined in
 * file 'LICENSE', which is part of this source code package.
 *
 * @copyright 2017 Copyright(c) - All rights reserved.
 * @author    YNLO-Ultratech Development Team <developer@ynloultratech.com>
 * @package   restful-platform
 */

namespace Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema\Model\Describer;

use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\ReadOnly;
use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\Type;
use JMS\Serializer\Metadata\VirtualPropertyMetadata;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema\Model\DescribeContext;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema\Model\ModelDescriberInterface;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema\Model\ModelPropertySchema;
use Ynlo\RestfulPlatformBundle\Util\AnnotationReader;
use Ynlo\RestfulPlatformBundle\Util\SerializerReader;

class JMSSerializerDescriber implements ModelDescriberInterface
{
    /**
     * @inheritDoc
     */
    public function describe(ModelPropertySchema $property, DescribeContext $context)
    {
        /** @var Type $serializerType */
        if (!$property->getType()) {
            $type = $this->getSerializerType($context->getProperty());

            //support for DateTime<’format’>
            if (preg_match('/^DateTime/', $type)) {
                $property->setFormat(ModelPropertySchema::FORMAT_DATETIME);
                if (preg_match('/<\'?([^>]+)\'?>$/', $type, $matches)) {
                    $property->setFormat($matches[1]);
                }
                $type = ModelPropertySchema::TYPE_STRING;
            }

            //Support for array<T> and array<K,T>
            if (preg_match('/^[array|ArrayCollection]/', $type)) {
                if (preg_match('/<\'?([^>]+)\'?>$/', $type, $matches)) {
                    $itemType = preg_replace('/\s+/', '', $matches[1]);

                    $format = null;
                    if (preg_match('/<([^<>]+)>/', $type, $matches)) {
                        $format = $matches[1];
                    }

                    if (strpos($itemType, ',') !== false) {
                        list($keyType, $itemType) = explode(',', $itemType);
                        $property->setKeyType($keyType);
                    }

                    if (preg_match('/string\s?,\s?string/', $format)) {
                        $property->setExample(['key' => 'value']);
                    }

                    $type = ModelPropertySchema::TYPE_ARRAY;
                    $property->setItemType($itemType);
                }
            }

            //common types to SwaggerTypes
            switch ($type) {
                case 'int':
                    $type = ModelPropertySchema::TYPE_INTEGER;
                    break;
                case 'bool':
                    $type = ModelPropertySchema::TYPE_BOOLEAN;
                    break;
                case 'double':
                    $type = ModelPropertySchema::TYPE_NUMBER;
                    $property->setFormat(ModelPropertySchema::FORMAT_DOUBLE);
                    break;
                case 'float':
                    $type = ModelPropertySchema::TYPE_NUMBER;
                    $property->setFormat(ModelPropertySchema::FORMAT_FLOAT);
                    break;
            }

            $property->setType($type);
        }

        $property->setReadOnly($this->isReadOnly($context->getProperty()));
        $property->setName($this->getName($context->getProperty()));
        $property->setGroups($this->getGroups($context->getProperty()));
    }

    /**
     * @inheritDoc
     */
    public function supports(ModelPropertySchema $property, DescribeContext $context)
    {
        return true;
    }

    protected function getGroups($property)
    {
        $groups = [];
        if ($property instanceof \Reflector) {
            if ($annotation = AnnotationReader::getAnnotationFor($property, Groups::class)) {
                $groups = $annotation->groups ?? [];
            }
        }

        if ($property instanceof VirtualPropertyMetadata) {
            $groups = $property->groups ?? [];
        }

        return $groups;
    }

    protected function getName($property)
    {
        $name = '';
        if ($property instanceof \ReflectionProperty || $property instanceof \ReflectionMethod) {
            /** @var SerializedName $serializedName */
            if ($serializedName = AnnotationReader::getAnnotationFor($property, SerializedName::class)) {
                $name = $serializedName->name;
            } else {
                $name = SerializerReader::getSerializedName($property);
            }
        }

        if ($property instanceof VirtualPropertyMetadata) {
            $name = $property->serializedName;
        }

        return $name;
    }

    protected function getSerializerType($property)
    {
        $type = null;
        if ($property instanceof \Reflector) {
            if ($annotation = AnnotationReader::getAnnotationFor($property, Type::class)) {
                $type = $annotation->name;
            }
        }

        if ($property instanceof VirtualPropertyMetadata) {
            $type = $property->type ?: 'string';
        }

        return $type;
    }

    protected function isReadOnly($property)
    {
        $readOnly = false;
        if ($property instanceof \Reflector) {
            if (AnnotationReader::getAnnotationFor($property, ReadOnly::class)) {
                $readOnly = true;
            }
        }

        if ($property instanceof VirtualPropertyMetadata) {
            $readOnly = $property->readOnly;
        }

        return $readOnly;
    }
}