<?php

/**
 * LICENSE: This file is subject to the terms and conditions defined in
 * file 'LICENSE', which is part of this source code package.
 *
 * @copyright 2017 Copyright(c) - All rights reserved.
 * @author    YNLO-Ultratech Development Team <developer@ynloultratech.com>
 * @package   restful-platform
 */

namespace Ynlo\RestfulPlatformBundle\Component\TaggedServices;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class TaggedServicesCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws \InvalidArgumentException
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('tagged_services')) {
            return;
        }

        $manager = $container->getDefinition('tagged_services');

        $definitions = $container->getDefinitions();
        foreach ($definitions as $id => $definition) {
            foreach ($definition->getTags() as $tagName => $tagAttributes) {
                $manager->addMethodCall(
                    'addSpecification',
                    [$id, $tagName, $tagAttributes[0]]
                );
            }
        }
    }
}
