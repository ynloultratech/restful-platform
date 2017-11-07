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

namespace Ynlo\RestfulPlatformBundle\Hateoas;

use Doctrine\ORM\Proxy\Proxy;
use Hateoas\Configuration\Metadata\ClassMetadataInterface;
use Hateoas\Configuration\Metadata\ConfigurationExtensionInterface;
use Hateoas\Configuration\Relation;
use Ynlo\RestfulPlatformBundle\MediaServer\MediaFileInterface;

class MediaFileConfigurationExtension implements ConfigurationExtensionInterface
{
    /**
     * {@inheritDoc}
     */
    public function decorate(ClassMetadataInterface $classMetadata)
    {
        if (is_subclass_of($classMetadata->getName(), MediaFileInterface::class, true)) {

            $reflection = new \ReflectionClass($classMetadata->getName());

            if ($reflection->isAbstract() || $reflection->isSubclassOf(Proxy::class)) {
                return;
            }

            foreach ($classMetadata->getRelations() as $relation) {
                if ($relation->getName() === 'download') {
                    return;
                }
            }

            $classMetadata->addRelation(
                new Relation(
                    'download',
                    'expr(object.getDownloadUrl())'
                )
            );
        }
    }
}