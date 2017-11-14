<?php
/**
 * LICENSE: This file is subject to the terms and conditions defined in
 * file 'LICENSE', which is part of this source code package.
 *
 * @copyright 2017 Copyright(c) - All rights reserved.
 * @author    YNLO-Ultratech Development Team <developer@ynloultratech.com>
 * @package   restful-platform
 */

namespace Ynlo\RestfulPlatformBundle\MediaServer;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\Mapping\ClassMetadata;
use Ynlo\RestfulPlatformBundle\Annotation\AttachMediaFile;
use Ynlo\RestfulPlatformBundle\Util\AnnotationReader;

/**
 * Manager to know wish entities and properties are mapped
 * as MediaFile. The mapping information is cached to improve performance
 */
class MediaServerMetadata
{
    /**
     * @var Registry
     */
    protected $doctrine;

    /**
     * @var string
     */
    protected $cacheDir;

    /**
     * @var array
     */
    protected $managedEntities = [];

    /**
     * @param Registry $doctrine
     * @param string   $cacheDir
     */
    public function __construct(Registry $doctrine, $cacheDir)
    {
        $this->doctrine = $doctrine;
        $this->cacheDir = $cacheDir;
    }

    /**
     * @param string $class
     *
     * @return bool
     */
    public function isMappedClass($class)
    {
        return isset($this->getManagedEntities()[$class]);
    }

    /**
     * @param string $class
     * @param string $property
     *
     * @return bool
     */
    public function isMappedProperty($class, $property)
    {
        return isset($this->getManagedEntities()[$class][$property]);
    }

    /**
     * @param $class
     * @param $property
     *
     * @return AttachMediaFile
     */
    public function getPropertyConfig($class, $property)
    {
        return $this->getManagedEntities()[$class][$property];
    }

    /**
     * @return array
     */
    public function getManagedEntities(): array
    {
        $this->initialize();

        return $this->managedEntities;
    }

    protected function initialize()
    {
        if (!empty($this->managedEntities)) {
            return;
        }

        $this->loadCache();

        if (!empty($this->managedEntities)) {
            return;
        }

        $meta = $this->doctrine->getManager()->getMetadataFactory()->getAllMetadata();
        /** @var ClassMetadata $m */
        foreach ($meta as $m) {
            $properties = $m->getAssociationNames();
            foreach ($properties as $property) {
                $targetClass = $m->getAssociationTargetClass($property);
                if (is_subclass_of($targetClass, MediaFileInterface::class, true)) {

                    $annotation = AnnotationReader::getAnnotationFor(
                        $m->reflClass->getProperty($property),
                        AttachMediaFile::class
                    );
                    if (!$annotation) {
                        $annotation = new AttachMediaFile();
                    }

                    $this->managedEntities[$m->name][$property] = $annotation;
                }
            }
        }
        $this->saveCache();
    }

    /**
     * remove the specification cache
     */
    public function clearCache()
    {
        @unlink($this->cacheFileName());
        $this->initialize();
    }

    /**
     * @return string
     */
    protected function cacheFileName()
    {
        return $this->cacheDir.DIRECTORY_SEPARATOR.'media_server.meta';
    }


    protected function loadCache()
    {
        if (file_exists($this->cacheFileName())) {
            $content = @file_get_contents($this->cacheFileName());
            if ($content) {
                $this->managedEntities = unserialize($content, [AttachMediaFile::class]);
            }
        }
    }

    protected function saveCache()
    {
        file_put_contents($this->cacheFileName(), serialize($this->managedEntities));
    }
}