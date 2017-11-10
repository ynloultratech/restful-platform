<?php

namespace Tests\Serializer;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\ClassMetadataFactory;
use JMS\Serializer\Construction\ObjectConstructorInterface;
use JMS\Serializer\DeserializationContext;
use JMS\Serializer\Metadata\ClassMetadata;
use JMS\Serializer\VisitorInterface;
use Symfony\Bridge\Doctrine\ManagerRegistry;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Tests\Fixtures\Model\User;
use Ynlo\RestfulPlatformBundle\Serializer\UnserializeObjectConstructor;
use PHPUnit\Framework\TestCase;

class UnserializeObjectConstructorTest extends TestCase
{
    /**
     * @var ContainerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $container;

    /**
     * @var ManagerRegistry|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $registry;

    /**
     * @var ObjectConstructorInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $fallback;

    /**
     * @var VisitorInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $visitor;

    /**
     * @var ClassMetadata|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $metadata;

    /**
     * @var DeserializationContext|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $context;

    /**
     * @var UnserializeObjectConstructor|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $unserializeConstructor;

    /**
     * @var object
     */
    protected $build;

    public function setUp()
    {
        $this->registry = self::createMock(ManagerRegistry::class);
        $this->fallback = self::createMock(ObjectConstructorInterface::class);
        $this->container = self::createMock(ContainerInterface::class);
        $this->unserializeConstructor = new UnserializeObjectConstructor($this->registry, $this->fallback);
        $this->unserializeConstructor->setContainer($this->container);
        $this->visitor = self::createMock(VisitorInterface::class);
        $this->metadata = self::createMock(ClassMetadata::class);
        $this->metadata->reflection = new \ReflectionClass(\stdClass::class);
        $this->metadata->name = \stdClass::class;
        $this->context = self::createMock(DeserializationContext::class);
        $this->build = new \stdClass();
    }

    public function testConstruct_WithConstructorAnnotation()
    {
        $constructor = self::createMock(ObjectConstructorInterface::class);

        $this->container->method('get')
                        ->with('user_constructor')
                        ->willReturn($constructor);

        $constructor->expects(self::once())
                    ->method('construct')
                    ->with($this->visitor, $this->metadata, [], [], $this->context)->willReturn($this->build);

        $this->metadata->reflection = new \ReflectionClass(User::class);

        $buildObject = $this->unserializeConstructor
            ->construct($this->visitor, $this->metadata, [], [], $this->context);

        self::assertEquals($this->build, $buildObject);
    }

    public function testConstruct_WithConstructorAnnotationAndInvalidConstructor()
    {
        $this->container->method('get')
                        ->with('user_constructor')
                        ->willReturn(new \stdClass());

        $this->metadata->reflection = new \ReflectionClass(User::class);

        $this->metadata->name = 'User';

        self::expectException(\RuntimeException::class);

        self::expectExceptionMessage(
            sprintf(
                'Object constructor for %s should implements %s',
                'User',
                ObjectConstructorInterface::class
            )
        );

        $this->unserializeConstructor
            ->construct($this->visitor, $this->metadata, [], [], $this->context);
    }

    public function testConstruct_FallbackNoObjectManager()
    {
        $this->registry->expects(self::once())->method('getManagerForClass')
                       ->with(\stdClass::class)
                       ->willReturn(null);

        $this->fallback->expects(self::once())
                       ->method('construct')
                       ->with($this->visitor, $this->metadata, [], [], $this->context)
                       ->willReturn($this->build);

        $buildObject = $this->unserializeConstructor
            ->construct($this->visitor, $this->metadata, [], [], $this->context);

        self::assertEquals($this->build, $buildObject);
    }

    public function testConstruct_FallbackForMappedSuperclass()
    {
        $manager = self::createMock(ObjectManager::class);

        $metadataFactory = self::createMock(ClassMetadataFactory::class);
        $metadataFactory->expects(self::once())->method('isTransient')->with($this->metadata->name)->willReturn(true);

        $manager->expects(self::once())->method('getMetadataFactory')->willReturn($metadataFactory);

        $this->registry->expects(self::once())->method('getManagerForClass')
                       ->with(\stdClass::class)
                       ->willReturn($manager);

        $this->fallback->expects(self::once())
                       ->method('construct')
                       ->with($this->visitor, $this->metadata, [], [], $this->context)
                       ->willReturn($this->build);

        $buildObject = $this->unserializeConstructor
            ->construct($this->visitor, $this->metadata, [], [], $this->context);

        self::assertEquals($this->build, $buildObject);
    }

    public function testConstruct_LoadFromIdentifier()
    {
        $manager = self::createMock(EntityManagerInterface::class);

        $metadataFactory = self::createMock(ClassMetadataFactory::class);
        $metadataFactory->expects(self::once())->method('isTransient')->with($this->metadata->name)->willReturn(false);

        $manager->expects(self::once())
                ->method('getReference')
                ->with($this->metadata->name, 1)
                ->willReturn($this->build);

        $manager->expects(self::once())->method('getMetadataFactory')->willReturn($metadataFactory);

        $this->registry->expects(self::once())->method('getManagerForClass')
                       ->with(\stdClass::class)
                       ->willReturn($manager);

        $buildObject = $this->unserializeConstructor
            ->construct($this->visitor, $this->metadata, 1, [], $this->context);

        self::assertEquals($this->build, $buildObject);
    }

    public function testConstruct_FallbackWithMissingIdentifier()
    {
        $manager = self::createMock(EntityManagerInterface::class);

        $metadataFactory = self::createMock(ClassMetadataFactory::class);
        $metadataFactory->expects(self::once())->method('isTransient')->with($this->metadata->name)->willReturn(false);

        $classMetadata = self::createMock(\Doctrine\ORM\Mapping\ClassMetadata::class);
        $classMetadata->expects(self::once())->method('getIdentifierFieldNames')->willReturn(['id']);

        $manager->expects(self::once())
                ->method('getClassMetadata')
                ->with($this->metadata->name)
                ->willReturn($classMetadata);

        $manager->expects(self::once())->method('getMetadataFactory')->willReturn($metadataFactory);

        $this->registry->expects(self::once())->method('getManagerForClass')
                       ->with(\stdClass::class)
                       ->willReturn($manager);

        $buildObject = $this->unserializeConstructor
            ->construct($this->visitor, $this->metadata, [], [], $this->context);

        self::assertEquals($this->build, $buildObject);
    }

    public function testConstruct_FindInRepository()
    {
        $manager = self::createMock(EntityManagerInterface::class);

        $repository = self::createMock(EntityRepository::class);

        $repository->expects(self::once())->method('find')->with(['id' => 1])->willReturn($this->build);

        $metadataFactory = self::createMock(ClassMetadataFactory::class);
        $metadataFactory->expects(self::once())->method('isTransient')->with($this->metadata->name)->willReturn(false);

        $classMetadata = self::createMock(\Doctrine\ORM\Mapping\ClassMetadata::class);
        $classMetadata->expects(self::once())->method('getIdentifierFieldNames')->willReturn(['id']);

        $manager->expects(self::once())
                ->method('getClassMetadata')
                ->with($this->metadata->name)
                ->willReturn($classMetadata);

        $manager->expects(self::once())
                ->method('getRepository')
                ->with($this->metadata->name)
                ->willReturn($repository);

        $manager->expects(self::once())
                ->method('initializeObject')
                ->with($this->build);

        $manager->expects(self::once())->method('getMetadataFactory')->willReturn($metadataFactory);

        $this->registry->expects(self::once())->method('getManagerForClass')
                       ->with(\stdClass::class)
                       ->willReturn($manager);

        $buildObject = $this->unserializeConstructor
            ->construct($this->visitor, $this->metadata, ['id' => 1], [], $this->context);

        self::assertEquals($this->build, $buildObject);
    }

    public function testConstruct_FindInRepositoryNotFound()
    {
        $manager = self::createMock(EntityManagerInterface::class);

        $repository = self::createMock(EntityRepository::class);

        $repository->expects(self::once())->method('find')->with(['id' => 1])->willReturn(null);

        $metadataFactory = self::createMock(ClassMetadataFactory::class);
        $metadataFactory->expects(self::once())->method('isTransient')->with($this->metadata->name)->willReturn(false);

        $classMetadata = self::createMock(\Doctrine\ORM\Mapping\ClassMetadata::class);
        $classMetadata->expects(self::once())->method('getIdentifierFieldNames')->willReturn(['id']);

        $manager->expects(self::once())
                ->method('getClassMetadata')
                ->with($this->metadata->name)
                ->willReturn($classMetadata);

        $manager->expects(self::once())
                ->method('getRepository')
                ->with($this->metadata->name)
                ->willReturn($repository);

        $manager->expects(self::never())
                ->method('initializeObject');

        $manager->expects(self::once())->method('getMetadataFactory')->willReturn($metadataFactory);

        $this->registry->expects(self::once())->method('getManagerForClass')
                       ->with(\stdClass::class)
                       ->willReturn($manager);

        $buildObject = $this->unserializeConstructor
            ->construct($this->visitor, $this->metadata, ['id' => 1], [], $this->context);

        self::assertEquals($this->build, $buildObject);
    }
}
