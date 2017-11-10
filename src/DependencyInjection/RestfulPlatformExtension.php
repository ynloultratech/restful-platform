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

namespace Ynlo\RestfulPlatformBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class RestfulPlatformExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration($container->getParameter('kernel.debug'));
        $config = $this->processConfiguration($configuration, $configs);

        $container->setParameter('restful_platform.config', $config);

        $mediaServer = $config['media_server'] ?? [];
        $container->setParameter('restful_platform.config.media_server', $mediaServer);

        $container->setParameter(
            'restful_platform.exception_controller',
            'restful_platform.exception_controller:showAction'
        );

        if (!$mediaServer || empty($mediaServer)) {
            $container->removeDefinition('restful_platform.media_file_api');
            $container->removeDefinition('restful_platform.media_storage.default');
            $container->removeDefinition('restful_platform.media_storage.local');
        }

        $configDir = __DIR__.'/../Resources/config';
        $loader = new YamlFileLoader($container, new FileLocator($configDir));
        $loader->load('services.yml');

        //in production does not clear cache using request events
        if (!$container->getParameter('kernel.debug')) {
            $container->getDefinition('restful_platform.cache_warmer')->clearTag('kernel.event_subscriber');
            $container->getDefinition('restful_platform.media_server.cache_warmer')->clearTag('kernel.event_subscriber');
        }
    }
}
