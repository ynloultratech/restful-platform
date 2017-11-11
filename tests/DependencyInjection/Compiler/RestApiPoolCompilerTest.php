<?php

namespace Tests\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Ynlo\RestfulPlatformBundle\DependencyInjection\Compiler\RestApiPoolCompiler;
use PHPUnit\Framework\TestCase;

class RestApiPoolCompilerTest extends TestCase
{
    public function testProcess()
    {
        $container = self::createMock(ContainerBuilder::class);

        $pool = self::createMock(Definition::class);

        $definitions = [
            'api1' => [],
            'api2' => [],
        ];

        $container->method('hasDefinition')->with('restful_platform.api_pool')->willReturn(true);
        $container->method('getDefinition')->with('restful_platform.api_pool')->willReturn($pool);
        $container->method('findTaggedServiceIds')->with('restful_platform.rest_api')->willReturn($definitions);

        $pool->expects(self::exactly(2))
             ->method('addMethodCall')
             ->withConsecutive(
                 ['addApi', [ new Reference('api1')]],
                 ['addApi', [ new Reference('api2')]]
             );

        $compilerPass = new RestApiPoolCompiler();
        $compilerPass->process($container);
    }

    public function testProcess_WithoutTaggedServiceRegistered()
    {
        $container = self::createMock(ContainerBuilder::class);
        $container->method('hasDefinition')->with('restful_platform.api_pool')->willReturn(false);
        $container->expects(self::never())->method('getDefinition');

        $compilerPass = new RestApiPoolCompiler();
        $compilerPass->process($container);
    }
}
