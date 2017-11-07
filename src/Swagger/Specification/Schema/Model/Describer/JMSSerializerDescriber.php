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
                if (preg_match('/<\'?([^>]+)\'?>$/', $type, $matches)) {
                    $property->setFormat((new \DateTime())->format($matches[1]));
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
                    $type = 'integer';
                    break;
                case 'double':
                case 'float':
                    $type = 'string';
                    $property->setFormat(ModelPropertySchema::FORMAT_DOUBLE);
                    break;
                case 'DateTime':
                case 'DateTimeImmutable':
                    $type = ModelPropertySchema::TYPE_STRING;
                    $property->setFormat(ModelPropertySchema::FORMAT_DATETIME);
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
        if ($property instanceof \Reflector) {
            /** @var Groups $groups */
            if ($groups = AnnotationReader::getAnnotationFor($property, Groups::class)) {
                return $groups->groups ?: [];
            }
        }

        if ($property instanceof VirtualPropertyMetadata) {
            return $property->groups ?: [];
        }

        return [];
    }

    protected function getName($property)
    {
        if ($property instanceof \Reflector) {
            /** @var SerializedName $serializedName */
            if ($serializedName = AnnotationReader::getAnnotationFor($property, SerializedName::class)) {
                return $serializedName->name;
            }

            return SerializerReader::getSerializedName($property);
        }

        if ($property instanceof VirtualPropertyMetadata) {
            return $property->serializedName;
        }

        return $property->name;
    }

    protected function getSerializerType($property)
    {
        if ($property instanceof \Reflector) {
            /** @var Type $type */
            if ($type = AnnotationReader::getAnnotationFor($property, Type::class)) {
                return $type->name;
            }
        }

        if ($property instanceof VirtualPropertyMetadata) {
            return $property->type ?: 'string';
        }

        return null;
    }

    protected function isReadOnly($property)
    {
        if ($property instanceof \Reflector) {
            if (AnnotationReader::getAnnotationFor($property, ReadOnly::class)) {
                return true;
            }
        }

        if ($property instanceof VirtualPropertyMetadata) {
            return $property->readOnly;
        }

        return false;
    }
}