<?php

/**
 * LICENSE: This file is subject to the terms and conditions defined in
 * file 'LICENSE', which is part of this source code package.
 *
 * @copyright 2017 Copyright(c) - All rights reserved.
 * @author    YNLO-Ultratech Development Team <developer@ynloultratech.com>
 * @package   restful-platform
 */

namespace Ynlo\RestfulPlatformBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\NodeBuilder;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    const STORAGE_LOCAL = 'local';

    const STORAGE_PROVIDERS = [
        self::STORAGE_LOCAL,
    ];

    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        /** @var NodeBuilder $rootNode */
        $rootNode = $treeBuilder->root('restful_platform')->addDefaultsIfNotSet()->children();

        $doc = $rootNode->arrayNode('documentation')->info('The documentation used as base');
        $this->configureDocumentation($doc->children());


        $rootNode->scalarNode('host')
                 ->info('Define the API host')
                 ->example('api.domain.com');

        $rootNode->scalarNode('base_path')
                 ->info('Define the API base bath')
                 ->example('/api/{version}');


        $this->configureMediaServer($rootNode);

        //TODO: versioning API
        //        $versioning = $rootNode->arrayNode('versioning')->info('Versioning api')->children();
        //
        //        $versioning->enumNode('in')->values(['query', 'path', 'header'])
        //                 ->info('Where the version param is present')
        //                 ->defaultValue('path');
        //
        //        $versioning->scalarNode('name')->info('Name of the parameter')->defaultValue('version');
        //        $versioning->scalarNode('default')->info('Default version when the user does not specify any (only works in query and header)');


        return $treeBuilder;
    }

    protected function configureDocumentation(NodeBuilder $docNode)
    {
        $info = $docNode->arrayNode('info')->children();

        $info->scalarNode('title')->defaultValue('API');
        $info->scalarNode('description');
        $info->scalarNode('version');

        /** @var NodeBuilder $tags */
        $tags = $docNode->arrayNode('tags')
                        ->info(
                            'A list of tags used by the specification with additional metadata. 
                        The order of the tags can be used to reflect on their order by the parsing tools'
                        )
                        ->useAttributeAsKey('id')
                        ->prototype('variable');
    }


    protected function configureMediaServer(NodeBuilder $rootNode)
    {
        $mediaServer = $rootNode->arrayNode('media_server')
                                ->info('Serve media files, upload & update assets')
                                ->children();

        $mediaServer
            ->scalarNode('class')
            ->info('Entity class to persist and get media files relations')
            ->cannotBeEmpty()
            ->isRequired()
            ->end();

        $mediaServer->scalarNode('path')
                    ->defaultValue('/assets')
                    ->cannotBeEmpty()
                    ->info('API end-point to interact with media objects, upload, get details, delete etc.');

        $mediaServer->variableNode('actions')
                    ->defaultValue(['get', 'create', 'update'])
                    ->info(
                        'Allowed actions directly in the media file API end-point. 
                    Can be one or multiple of the following actions,"list", "get", "create","update", "delete"'
                    );

        $mediaServer->scalarNode('default_storage')->isRequired()->example('default');

        /** @var NodeBuilder $mediaStorage */
        $mediaStorage = $mediaServer->arrayNode('storage')
                                    ->info('Media storage to save and fetch media files')
                                    ->useAttributeAsKey('id')
                                    ->isRequired()
                                    ->requiresAtLeastOneElement()
                                    ->prototype('array')
                                    ->children();

        //local storage
        $localStorage = $mediaStorage
            ->arrayNode(self::STORAGE_LOCAL)
            ->info('Provide local storage capabilities, for public or private files')->children();

        $localStorage->scalarNode('provider')->defaultValue('local');

        $localStorage->booleanNode('private')
                     ->defaultFalse()
                     ->info('Mark this storage as private, otherwise is used as public storage');

        $localStorage->scalarNode('dir_name')
                     ->info(
                         'Absolute local path to store files,
                          NOTE: should be a public accessible path for public assets,
                      and non public accessible path for private assets'
                     )
                     ->example('PRIVATE: "%kernel.project_dir%/media" or PUBLIC: "%kernel.project_dir%/web/media"');

        $localStorage->scalarNode('base_url')
                     ->info('Absolute url to resolve PUBLIC files')
                     ->example('https://example.com/media/');

        $localStorage->scalarNode('route_name')
                     ->defaultValue('restful_platform_get_media_file')
                     ->info(
                         'Name of the route to use to resolve PRIVATE assets, 
                     this route will be pre-signed with the configured `signature_parameter`'
                     );

        $localStorage->scalarNode('signature_parameter')
                     ->defaultValue('_hash')
                     ->info('Name of the signature param to store the digital signature');

        $localStorage->integerNode('signature_max_age')
                     ->defaultValue(3600)
                     ->min(1)
                     ->max(86400)
                     ->info('Age in seconds of each signature');
    }
}
