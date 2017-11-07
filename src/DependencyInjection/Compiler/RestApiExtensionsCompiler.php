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

class RestApiExtensionsCompiler implements CompilerPassInterface
{
    /**
     * @inheritdoc
     */
    public function process(ContainerBuilder $container)
    {
        $endpoints = $container->findTaggedServiceIds('restful_platform.rest_api');

        $extensions = $container->findTaggedServiceIds('restful_platform.rest_api_extension');

        foreach ($endpoints as $id => $tags) {
            $definition = $container->findDefinition($id);
            foreach ($extensions as $extId => $extTags) {
                $definition->addMethodCall('addExtension', [new Reference($extId)]);
            }
        }
    }
}