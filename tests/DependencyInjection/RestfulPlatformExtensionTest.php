<?php

namespace Tests\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Tests\Fixtures\Model\User;
use Ynlo\RestfulPlatformBundle\DependencyInjection\RestfulPlatformExtension;
use PHPUnit\Framework\TestCase;
use Mockery as m;

class RestfulPlatformExtensionTest extends TestCase
{
    use m\Adapter\Phpunit\MockeryPHPUnitIntegration;

    /**
     * @var ContainerBuilder|m\Mock
     */
    protected $container;

    protected function setUp()
    {
        $this->container = m::mock(ContainerBuilder::class)->makePartial();
    }

    public function testLoad()
    {
        $config = [];
        $extension = new RestfulPlatformExtension();

        $this->container->shouldReceive('fileExists')->andReturn(true);
        $this->container->shouldReceive('setDefinition');
        $this->container->shouldReceive('setAlias');

        $this->container->shouldReceive('getParameter')
                        ->withArgs(['kernel.debug'])
                        ->andReturn(true);

        $this->container->shouldReceive('setParameter')
                        ->withArgs(
                            function ($arg1) {
                                return $arg1 === 'restful_platform.config';
                            }
                        );

        $this->container->shouldReceive('setParameter')
                        ->withArgs(['restful_platform.config.media_server', []]);

        $this->container->shouldReceive('setParameter')
                        ->withArgs(
                            [
                                'restful_platform.exception_controller',
                                'restful_platform.exception_controller:showAction',
                            ]
                        );

        $this->container->shouldReceive('removeDefinition')
                        ->withArgs(['restful_platform.media_file_api']);

        $this->container->shouldReceive('removeDefinition')
                        ->withArgs(['restful_platform.media_storage.default']);

        $this->container->shouldReceive('removeDefinition')
                        ->withArgs(['restful_platform.media_storage.local']);

        $this->container->shouldReceive('getDefinition')
                        ->withArgs(['restful_platform.cache_warmer']);

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

        $this->container->shouldReceive('setParameter')->withArgs(
            function ($arg) {
                return in_array(
                    $arg,
                    [
                        'restful_platform.config',
                        'restful_platform.config.media_server',
                        'restful_platform.exception_controller'
                    ]
                );
            }
        );

        $this->container->shouldReceive('getParameter')
                        ->withArgs(['kernel.debug'])
                        ->andReturn(true);

        $this->container->shouldNotReceive('removeDefinition')
                        ->withArgs(['restful_platform.media_file_api']);

        $extension->load([$config], $this->container);
    }

    public function testLoad_InProduction()
    {
        $config = [];
        $extension = new RestfulPlatformExtension();

        $this->container->shouldReceive('getParameter')
                        ->withArgs(['kernel.debug'])
                        ->andReturn(false);


        $this->container->shouldReceive('setParameter')->withArgs(
            function ($arg) {
                return in_array(
                    $arg,
                    [
                        'restful_platform.config',
                        'restful_platform.config.media_server',
                        'restful_platform.exception_controller'
                    ]
                );
            }
        );

        $definition1 = m::mock(Definition::class);
        $definition1->shouldReceive('clearTag')->withArgs(['kernel.event_subscriber']);

        $definition2 = m::mock(Definition::class);
        $definition2->shouldReceive('clearTag')->withArgs(['kernel.event_subscriber']);

        $this->container->shouldReceive('getDefinition')
                        ->withArgs(['restful_platform.cache_warmer'])
                        ->andReturn($definition1);

        $this->container->shouldReceive('getDefinition')
                        ->withArgs(['restful_platform.media_server.cache_warmer'])
                        ->andReturn($definition2);

        $extension->load([$config], $this->container);
    }
}
