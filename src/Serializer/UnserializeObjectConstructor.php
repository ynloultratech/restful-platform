<?php
/**
 * LICENSE: This file is subject to the terms and conditions defined in
 * file 'LICENSE', which is part of this source code package.
 *
 * @copyright 2017 Copyright(c) - All rights reserved.
 * @author    YNLO-Ultratech Development Team <developer@ynloultratech.com>
 * @package   restful-platform
 */

namespace Ynlo\RestfulPlatformBundle\Serializer;

use JMS\Serializer\Construction\ObjectConstructorInterface;
use JMS\Serializer\DeserializationContext;
use JMS\Serializer\Metadata\ClassMetadata;
use JMS\Serializer\VisitorInterface;
use Symfony\Bridge\Doctrine\ManagerRegistry;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Ynlo\RestfulPlatformBundle\Annotation\DeserializerObjectConstructor;
use Ynlo\RestfulPlatformBundle\Util\AnnotationReader;

/**
 * This Serializer Constructor is based on DoctrineObjectConstructor
 * with support to execute the original __constructor() in not managed entities
 */
class UnserializeObjectConstructor implements ObjectConstructorInterface, ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @var ManagerRegistry
     */
    protected $managerRegistry;

    /**
     * @var ObjectConstructorInterface
     */
    protected $fallbackConstructor;

    public function __construct(ManagerRegistry $managerRegistry, ObjectConstructorInterface $fallbackConstructor)
    {
        $this->managerRegistry = $managerRegistry;
        $this->fallbackConstructor = $fallbackConstructor;
    }

    /**
     * {@inheritdoc}
     */
    public function construct(VisitorInterface $visitor, ClassMetadata $metadata, $data, array $type, DeserializationContext $context)
    {
        /** @var  DeserializerObjectConstructor $desConsAnnotation */
        $desConsAnnotation = AnnotationReader::getAnnotationFor(
            $metadata->reflection,
            DeserializerObjectConstructor::class
        );

        if ($desConsAnnotation && $desConsAnnotation->service) {
            $desCons = $this->container->get($desConsAnnotation->service);
            if ($desCons instanceof ObjectConstructorInterface) {
                return $desCons->construct($visitor, $metadata, $data, $type, $context);
            }
            throw new \RuntimeException(
                sprintf(
                    'Object constructor for %s should implements %s',
                    $metadata->name,
                    ObjectConstructorInterface::class
                )
            );
        }

        // Locate possible ObjectManager
        $objectManager = $this->managerRegistry->getManagerForClass($metadata->name);

        if (!$objectManager) {
            // No ObjectManager found, proceed with normal deserialization
            return $this->fallbackConstructor->construct($visitor, $metadata, $data, $type, $context);
        }

        // Locate possible ClassMetadata
        $classMetadataFactory = $objectManager->getMetadataFactory();

        if ($classMetadataFactory->isTransient($metadata->name)) {
            // No ClassMetadata found, proceed with normal deserialization
            return $this->fallbackConstructor->construct($visitor, $metadata, $data, $type, $context);
        }

        // Managed entity, check for proxy load
        if (!is_array($data)) {
            // Single identifier, load proxy
            return $objectManager->getReference($metadata->name, $data);
        }

        // Fallback to default constructor if missing identifier(s)
        $classMetadata = $objectManager->getClassMetadata($metadata->name);
        $identifierList = [];

        foreach ($classMetadata->getIdentifierFieldNames() as $name) {
            if (!array_key_exists($name, $data)) {
                return new $metadata->name;
            }

            $identifierList[$name] = $data[$name];
        }

        // Entity update, load it from database
        $object = $objectManager->getRepository($metadata->name)->find($identifierList);

        if (null === $object) {
            return new $metadata->name;
        }

        $objectManager->initializeObject($object);

        return $object;
    }
}