<?php

namespace Ynlo\RestfulPlatformBundle\Tests\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Ynlo\RestfulPlatformBundle\DependencyInjection\Compiler\RestApiExtensionsCompiler;

class RestApiExtensionsCompilerTest extends TestCase
{
    public function testProcess()
    {
        $container = self::createMock(ContainerBuilder::class);

        $container->expects(self::at(0))
                  ->method('findTaggedServiceIds')
                  ->with('restful_platform.rest_api')
                  ->willReturn(
                      [
                          'api' => [],
                      ]
                  );

        $container->expects(self::at(1))
                  ->method('findTaggedServiceIds')
                  ->with('restful_platform.rest_api_extension')
                  ->willReturn(
                      [
                          'extension' => [],
                      ]
                  );

        $definition = self::createMock(Definition::class);
        $definition->method('addMethodCall')->with('addExtension', [new Reference('extension')]);

        $container->method('findDefinition')
                  ->with('api')
                  ->willReturn($definition);

        $compiler = new RestApiExtensionsCompiler();
        $compiler->process($container);
    }
}
