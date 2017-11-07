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

namespace Ynlo\RestfulPlatformBundle\EventListener\Doctrine;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Ynlo\RestfulPlatformBundle\MediaServer\MediaFileInterface;
use Ynlo\RestfulPlatformBundle\MediaServer\MediaStorageProviderInterface;

class MediaServerListener implements EventSubscriber, ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @inheritDoc
     */
    public function getSubscribedEvents()
    {
        return [
            Events::postLoad => 'postLoad',
            Events::preUpdate => 'preUpdate',
            Events::preRemove => 'preRemove',
        ];
    }

    public function postLoad(LifecycleEventArgs $event)
    {
        $object = $event->getObject();
        if ($object instanceof MediaFileInterface) {
            if ($provider = $this->getProviderByStorageId($object->getStorage())) {
                $object->setDownloadUrl($provider->getDownloadUrl($object));
            }
        }
    }

    /**
     * @param PreUpdateEventArgs $event
     */
    public function preUpdate(PreUpdateEventArgs $event)
    {
        $metadata = $this->container->get('restful_platform.media_server.metadata');
        $object = $event->getObject();
        $class = get_class($object);
        if ($metadata->isMappedClass($class)) {
            foreach ($event->getEntityChangeSet() as $name => $changeSet) {
                if ($metadata->isMappedProperty($class, $name)) {
                    $config = $metadata->getPropertyConfig($class, $name);
                    list(, $newValue) = $changeSet;

                    if ($newValue instanceof MediaFileInterface) {
                        //move from default provider to configured provider
                        if ($config->storage && $newValue->getStorage() !== $config->storage) {

                            $oldProvider = $this->getProviderByStorageId($newValue->getStorage());
                            $content = $oldProvider->read($newValue);

                            $newProvider = $this->getProviderByStorageId($config->storage);
                            $newProvider->save($newValue, $content);

                            $newValue->setDownloadUrl($newProvider->getDownloadUrl($newValue));
                            $newValue->setStorage($config->storage);
                            $newValue->used();

                            $oldProvider->remove($newValue);
                        }
                    }
                }
            }
        }
    }

    /**
     * @param LifecycleEventArgs $event
     */
    public function preRemove(LifecycleEventArgs $event)
    {
        $object = $event->getObject();
        if ($object instanceof MediaFileInterface) {
            if ($provider = $this->getProviderByStorageId($object->getStorage())) {
                $provider->remove($object);
            }
        }
    }

    /**
     * @param $id
     *
     * @return MediaStorageProviderInterface
     */
    protected function getProviderByStorageId($id)
    {
        return $this->container->get('restful_platform.media_storage_pool')->getByStorageId($id);
    }
}