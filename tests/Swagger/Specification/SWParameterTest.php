<?php

namespace Tests\Swagger\Specification;

use Ynlo\RestfulPlatformBundle\Swagger\Model\Operation;
use Ynlo\RestfulPlatformBundle\Swagger\Model\Parameter;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Operation\ParameterSpec;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema\SchemaSpec;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\SWParameter;
use PHPUnit\Framework\TestCase;

class SWParameterTest extends TestCase
{
    public function testInBody()
    {
        $paramSpec = (SWParameter::inBody(new SchemaSpec('User', [])));
        $decorator = (new ParameterSpec('body', $paramSpec))->getDecorator();
        $operation = new Operation();
        $decorator($operation);

        self::assertTrue($operation->getParameters()->get('body')->isRequired());
        self::assertEquals(Parameter::IN_BODY, $operation->getParameters()->get('body')->getIn());
    }

    public function testInPath()
    {
        $paramSpec = (SWParameter::inPath('string', 'datetime'));
        $decorator = (new ParameterSpec('id', $paramSpec))->getDecorator();
        $operation = new Operation();
        $decorator($operation);

        self::assertTrue($operation->getParameters()->get('id')->isRequired());
        self::assertEquals(Parameter::IN_PATH, $operation->getParameters()->get('id')->getIn());
        self::assertEquals('string', $operation->getParameters()->get('id')->getType());
        self::assertEquals('datetime', $operation->getParameters()->get('id')->getFormat());
    }

    public function testInForm()
    {
        $paramSpec = (SWParameter::inForm('string', 'datetime'));
        $decorator = (new ParameterSpec('id', $paramSpec))->getDecorator();
        $operation = new Operation();
        $decorator($operation);

        self::assertEquals(Parameter::IN_FORM, $operation->getParameters()->get('id')->getIn());
        self::assertEquals('string', $operation->getParameters()->get('id')->getType());
        self::assertEquals('datetime', $operation->getParameters()->get('id')->getFormat());
    }

    public function testInQuery()
    {
        $paramSpec = (SWParameter::inQuery('string', 'datetime'));
        $decorator = (new ParameterSpec('id', $paramSpec))->getDecorator();
        $operation = new Operation();
        $decorator($operation);

        self::assertFalse($operation->getParameters()->get('id')->isRequired());
        self::assertEquals(Parameter::IN_QUERY, $operation->getParameters()->get('id')->getIn());
        self::assertEquals('string', $operation->getParameters()->get('id')->getType());
        self::assertEquals('datetime', $operation->getParameters()->get('id')->getFormat());
    }

    public function testInHeader()
    {
        $paramSpec = (SWParameter::inHeader('string', 'datetime'));
        $decorator = (new ParameterSpec('id', $paramSpec))->getDecorator();
        $operation = new Operation();
        $decorator($operation);

        self::assertFalse($operation->getParameters()->get('id')->isRequired());
        self::assertEquals(Parameter::IN_HEADER, $operation->getParameters()->get('id')->getIn());
        self::assertEquals('string', $operation->getParameters()->get('id')->getType());
        self::assertEquals('datetime', $operation->getParameters()->get('id')->getFormat());
    }

    public function testType()
    {
        $paramSpec = (SWParameter::type('string', 'datetime'));
        $decorator = (new ParameterSpec('id', $paramSpec))->getDecorator();
        $operation = new Operation();
        $decorator($operation);

        self::assertEquals('string', $operation->getParameters()->get('id')->getType());
        self::assertEquals('datetime', $operation->getParameters()->get('id')->getFormat());
    }

    public function testRequired()
    {
        $requiredParam = (new ParameterSpec('id', [SWParameter::required()]))->getDecorator();
        $requiredParam2 = (new ParameterSpec('id2', [SWParameter::required(true)]))->getDecorator();
        $notRequiredParam = (new ParameterSpec('idNot', [SWParameter::required(false)]))->getDecorator();

        $operation = new Operation();

        $requiredParam($operation);
        $requiredParam2($operation);
        $notRequiredParam($operation);

        self::assertTrue($operation->getParameters()->get('id')->isRequired());
        self::assertTrue($operation->getParameters()->get('id2')->isRequired());
        self::assertFalse($operation->getParameters()->get('idNot')->isRequired());
    }

    public function testDescription()
    {
        $decorator = (new ParameterSpec('id', [SWParameter::description('description')]))->getDecorator();
        $operation = new Operation();
        $decorator($operation);

        self::assertEquals('description', $operation->getParameters()->get('id')->getDescription());
    }
}
