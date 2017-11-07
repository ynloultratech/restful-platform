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

namespace Ynlo\RestfulPlatformBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Ynlo\RestfulPlatformBundle\Api\RestApiInterface;
use Ynlo\RestfulPlatformBundle\Component\TaggedServices\TaggedServicesCompilerPass;
use Ynlo\RestfulPlatformBundle\DependencyInjection\Compiler\MediaStorageCompiler;
use Ynlo\RestfulPlatformBundle\DependencyInjection\Compiler\RestApiExtensionsCompiler;
use Ynlo\RestfulPlatformBundle\DependencyInjection\Compiler\RestApiPoolCompiler;
use Ynlo\RestfulPlatformBundle\Util\SerializerReader;

class RestfulPlatformBundle extends Bundle
{
    public function boot()
    {
        SerializerReader::$namingStrategy = $this->container->get('jms_serializer.naming_strategy');
    }

    /**
     * @inheritDoc
     */
    public function build(ContainerBuilder $container)
    {
        $container->registerForAutoconfiguration(RestApiInterface::class)->addTag('restful_platform.rest_api');
        $container->addCompilerPass(new TaggedServicesCompilerPass());
        $container->addCompilerPass(new RestApiPoolCompiler());
        $container->addCompilerPass(new RestApiExtensionsCompiler());
        $container->addCompilerPass(new MediaStorageCompiler());
    }
}