<?php

namespace Ynlo\RestfulPlatformBundle\Tests\Swagger\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Ynlo\RestfulPlatformBundle\Swagger\Model\Info;
use Ynlo\RestfulPlatformBundle\Swagger\Model\Path;
use Ynlo\RestfulPlatformBundle\Swagger\Model\SwaggerObject;
use PHPUnit\Framework\TestCase;
use Ynlo\RestfulPlatformBundle\Swagger\Model\Tag;

class SwaggerObjectTest extends TestCase
{
    public function testConstructor()
    {
        $swagger = new SwaggerObject();
        self::assertNotNull($swagger->getPaths());
        self::assertNotNull($swagger->getDefinitions());
        self::assertInstanceOf(Info::class, $swagger->getInfo());
        self::assertEquals(SwaggerObject::SWAGGER_v2, $swagger->getSwagger());
    }

    public function testSwagger()
    {
        $value = SwaggerObject::SWAGGER_v3;
        self::assertEquals($value, (new SwaggerObject())->setSwagger($value)->getSwagger());
    }

    public function testInfo()
    {
        $value = new Info();
        self::assertEquals($value, (new SwaggerObject())->setInfo($value)->getInfo());
    }

    public function testPaths()
    {
        $path = '/admin';
        $pathInstance = new Path($path);
        $value = new ArrayCollection([$path => $pathInstance]);
        self::assertEquals($value, (new SwaggerObject())->setPaths($value)->getPaths());
        self::assertEquals($path, (new SwaggerObject())->setPaths($value)->getPath($path)->getPath());
    }

    public function testHost()
    {
        $value = 'example.com';
        self::assertEquals($value, (new SwaggerObject())->setHost($value)->getHost());
    }

    public function testBasePath()
    {
        $value = '/v1/';
        self::assertEquals($value, (new SwaggerObject())->setBasePath($value)->getBasePath());
    }

    public function testScheme()
    {
        $value = ['http', 'https'];
        self::assertEquals($value, (new SwaggerObject())->setSchemes($value)->getSchemes());
    }

    public function testConsumes()
    {
        $value = ['json', 'http'];
        self::assertEquals($value, (new SwaggerObject())->setConsumes($value)->getConsumes());
    }

    public function testProduces()
    {
        $value = ['json', 'http'];
        self::assertEquals($value, (new SwaggerObject())->setProduces($value)->getProduces());
    }

    public function testDefinitions()
    {
        $value = new ArrayCollection();
        self::assertEquals($value, (new SwaggerObject())->setDefinitions($value)->getDefinitions());
    }

    public function testTags()
    {
        self::assertInternalType('array', (new SwaggerObject())->getTags());
        self::assertEmpty((new SwaggerObject())->getTags());
        self::assertNotEmpty((new SwaggerObject())->addTag('admin')->getTags());
        self::assertEquals(['admin' => new Tag('admin', 'some')], (new SwaggerObject())->addTag('admin', 'some')->getTags());
    }
}
