<?php

namespace Tests\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Ynlo\RestfulPlatformBundle\DependencyInjection\Compiler\MediaStorageCompiler;
use PHPUnit\Framework\TestCase;

class MediaStorageCompilerTest extends TestCase
{
    public function testProcess()
    {
        $container = self::createMock(ContainerBuilder::class);

        $pool = self::createMock(Definition::class);

        $definitions = [
            'service1' => [['alias' => 'local']],
            'service2' => [['alias' => 'cdn']],
            'service3' => [[]],
        ];

        $container->method('hasDefinition')->with('restful_platform.media_storage_pool')->willReturn(true);
        $container->method('getDefinition')->with('restful_platform.media_storage_pool')->willReturn($pool);
        $container->method('findTaggedServiceIds')->with('restful_platform.media_storage')->willReturn($definitions);

        $pool->expects(self::exactly(2))
             ->method('addMethodCall')
             ->withConsecutive(
                 ['add', ['local', new Reference('service1')]],
                 ['add', ['cdn', new Reference('service2')]]
             );

        self::expectExceptionMessage('`alias` is required for services tagged as `restful_platform.media_storage`');

        $compilerPass = new MediaStorageCompiler();
        $compilerPass->process($container);
    }

    public function testProcess_WithoutTaggedServiceRegistered()
    {
        $container = self::createMock(ContainerBuilder::class);
        $container->method('hasDefinition')->with('restful_platform.media_storage_pool')->willReturn(false);
        $container->expects(self::never())->method('getDefinition');

        $compilerPass = new MediaStorageCompiler();
        $compilerPass->process($container);
    }
}
