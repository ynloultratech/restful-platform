<?php

namespace Tests\Component\TaggedServices;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Ynlo\RestfulPlatformBundle\Component\TaggedServices\TaggedServicesCompilerPass;
use PHPUnit\Framework\TestCase;

class TaggedServicesCompilerPassTest extends TestCase
{
    public function testProcess()
    {
        $container = self::createMock(ContainerBuilder::class);

        $manager = self::createMock(Definition::class);

        $manager->expects(self::exactly(3))->method('addMethodCall')
                ->withConsecutive(
                    [
                        'addSpecification',
                        ['service1', 'some_tag', ['priority' => 255]],
                    ],
                    [
                        'addSpecification',
                        ['service1', 'other_tag', []],
                    ],
                    [
                        'addSpecification',
                        ['service2', 'some_tag', []],
                    ]
                );

        $definition1 = self::createMock(Definition::class);
        $definition2 = self::createMock(Definition::class);

        $definition1->method('getTags')->willReturn(['some_tag' => [['priority' => 255]], 'other_tag' => [[]]]);
        $definition2->method('getTags')->willReturn(['some_tag' => [[]]]);

        $definitions = [
            'service1' => $definition1,
            'service2' => $definition2,
        ];

        $container->method('hasDefinition')->with('tagged_services')->willReturn(true);
        $container->method('getDefinition')->with('tagged_services')->willReturn($manager);
        $container->method('getDefinitions')->willReturn($definitions);

        $compilerPass = new TaggedServicesCompilerPass();
        $compilerPass->process($container);
    }

    public function testProcess_WithoutTaggedServiceRegistered()
    {
        $container = self::createMock(ContainerBuilder::class);
        $container->method('hasDefinition')->with('tagged_services')->willReturn(false);
        $container->expects(self::never())->method('getDefinition');

        $compilerPass = new TaggedServicesCompilerPass();
        $compilerPass->process($container);
    }
}
