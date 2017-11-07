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

namespace Ynlo\RestfulPlatformBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class MediaStorageCompiler implements CompilerPassInterface
{
    /**
     * @inheritdoc
     */
    public function process(ContainerBuilder $container)
    {
        // always first check if the primary service is defined
        if (!$container->has('restful_platform.media_storage_pool')) {
            return;
        }

        $definition = $container->findDefinition('restful_platform.media_storage_pool');

        $taggedServices = $container->findTaggedServiceIds('restful_platform.media_storage');

        foreach ($taggedServices as $id => $tags) {
            if (isset($tags[0]['alias'])) {
                $definition->addMethodCall('add', [$tags[0]['alias'], new Reference($id)]);
            } else {
                throw new \LogicException('`alias` is required for services tagged as `restful_platform.media_storage`');
            }
        }
    }
}