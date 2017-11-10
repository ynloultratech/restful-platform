<?php

namespace Tests\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Tests\Fixtures\Model\User;
use Ynlo\RestfulPlatformBundle\DependencyInjection\RestfulPlatformExtension;
use PHPUnit\Framework\TestCase;

class RestfulPlatformExtensionTest extends TestCase
{
    /**
     * @var ContainerBuilder|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $container;

    protected function setUp()
    {
        $this->container = self::createMock(ContainerBuilder::class);
    }

    public function testLoad()
    {
        $config = [];
        $extension = new RestfulPlatformExtension();

        $this->container->expects(self::once())
                        ->method('getParameter')
                        ->with('kernel.debug')
                        ->willReturn(true);

        $this->container->expects(self::at(0))
                        ->method('setParameter')
                        ->with('restful_platform.config');

        $this->container->expects(self::at(1))
                        ->method('setParameter')
                        ->with('restful_platform.config.media_server');

        $this->container->expects(self::at(2))
                        ->method('setParameter')
                        ->with(
                            'restful_platform.exception_controller',
                            'restful_platform.exception_controller:showAction'
                        );

        $this->container->expects(self::atMost(3))
                        ->method('removeDefinition')
                        ->withConsecutive(
                            ['restful_platform.media_file_api'],
                            ['restful_platform.media_storage.default'],
                            ['restful_platform.media_storage.local']
                        );

        $this->container->expects(self::never())
                        ->method('getDefinition')
                        ->with('restful_platform.cache_warmer');

        $extension->load([$config], $this->container);
    }

    public function testLoad_WithMediaServer()
    {
        $config = [
            'media_server' => [
                'class' => User::class,
                'default_storage' => 'public',
                'storage' => [
                    'public' => [],
                ],
            ],
        ];
        $extension = new RestfulPlatformExtension();

        $this->container->expects(self::once())
                        ->method('getParameter')
                        ->with('kernel.debug')
                        ->willReturn(true);

        $this->container->expects(self::never())
                        ->method('removeDefinition')
                        ->with('restful_platform.media_file_api');

        $extension->load([$config], $this->container);
    }

    public function testLoad_InProduction()
    {
        $config = [];
        $extension = new RestfulPlatformExtension();

        $this->container->expects(self::once())
                        ->method('getParameter')
                        ->with('kernel.debug')
                        ->willReturn(false);


        $definition = self::createMock(Definition::class);
        $definition->expects(self::atMost(2))->method('clearTag');

        $this->container->expects(self::atMost(2))
                        ->method('getDefinition')
                        ->withConsecutive(
                            ['restful_platform.cache_warmer'],
                            ['restful_platform.media_server.cache_warmer']
                        )
                        ->willReturn($definition);

        $extension->load([$config], $this->container);
    }
}
