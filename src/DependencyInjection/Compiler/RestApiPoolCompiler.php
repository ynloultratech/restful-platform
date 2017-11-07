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

class RestApiPoolCompiler implements CompilerPassInterface
{
    /**
     * @inheritdoc
     */
    public function process(ContainerBuilder $container)
    {
        // always first check if the primary service is defined
        if (!$container->has('restful_platform.api_pool')) {
            return;
        }

        $definition = $container->findDefinition('restful_platform.api_pool');

        $taggedServices = $container->findTaggedServiceIds('restful_platform.rest_api');

        foreach ($taggedServices as $id => $tags) {
            $definition->addMethodCall('addApi', [new Reference($id)]);
        }
    }
}