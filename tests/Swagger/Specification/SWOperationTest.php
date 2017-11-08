<?php

namespace Tests\Swagger\Specification;

use Tests\Fixtures\Model\User;
use Ynlo\RestfulPlatformBundle\Swagger\Model\Operation;
use Ynlo\RestfulPlatformBundle\Swagger\Model\Parameter;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Parameter\InSpec;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema\DescriptionSpec;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema\PropertySpec;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema\SchemaSpec;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Schema\TypeSpec;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\SWOperation;
use PHPUnit\Framework\TestCase;

class SWOperationTest extends TestCase
{
    public function testTag()
    {
        $decorator = (SWOperation::tag('backend'))->getDecorator();
        $operation = new Operation();
        $decorator($operation);
        self::assertEquals('backend', $operation->getTags()->first());
    }

    public function testDescription()
    {
        $decorator = (SWOperation::description('description'))->getDecorator();
        $operation = new Operation();
        $decorator($operation);
        self::assertEquals('description', $operation->getDescription());
    }

    public function testParameter()
    {
        $decorator = (SWOperation::parameter('param1', [new InSpec('header')]))->getDecorator();
        $operation = new Operation();
        $decorator($operation);
        self::assertEquals('header', $operation->getParameters()->get('param1')->getIn());
    }

    public function testParameterInQuery()
    {
        $decorator = (SWOperation::parameterInQuery('param1', 'string'))->getDecorator();
        $operation = new Operation();
        $decorator($operation);
        self::assertEquals('query', $operation->getParameters()->get('param1')->getIn());
        self::assertEquals('string', $operation->getParameters()->get('param1')->getType());
    }

    public function testParameterInForm()
    {
        $decorator = (SWOperation::parameterInForm('param1', 'string'))->getDecorator();
        $operation = new Operation();
        $decorator($operation);
        self::assertEquals('formData', $operation->getParameters()->get('param1')->getIn());
        self::assertEquals('string', $operation->getParameters()->get('param1')->getType());
    }

    public function testParameterInPath()
    {
        $decorator = (SWOperation::parameterInPath('param1', 'string'))->getDecorator();
        $operation = new Operation();
        $decorator($operation);
        self::assertEquals('path', $operation->getParameters()->get('param1')->getIn());
        self::assertEquals('string', $operation->getParameters()->get('param1')->getType());
    }

    public function testResponse()
    {
        $decorator = (SWOperation::response(200, [new DescriptionSpec('success')]))->getDecorator();
        $operation = new Operation();
        $decorator($operation);
        self::assertEquals('success', $operation->getResponse(200)->getDescription());
    }

    public function testBody()
    {
        $usernameProperty = new PropertySpec('username', [new TypeSpec('string')]);
        $decorator = (SWOperation::body(new SchemaSpec('User', [$usernameProperty])))->getDecorator();
        $operation = new Operation();
        $decorator($operation);

        /** @var Parameter $body */
        $body = $operation->getParameters()->get('body');
        self::assertEquals('string', $body->getSchema()->getProperty('username')->getType());
    }

    public function testModel()
    {
        $decorator = (SWOperation::model(User::class, ['public']))->getDecorator();
        $operation = new Operation();
        $decorator($operation);

        /** @var Parameter $body */
        $body = $operation->getParameters()->get('body');
        self::assertNull($body->getSchema()->getProperty('username'));
        self::assertNotNull($body->getSchema()->getProperty('firstName'));
        self::assertNotNull($body->getSchema()->getProperty('lastName'));
    }

    public function testOperationId()
    {
        $decorator = (SWOperation::operationId('create_user'))->getDecorator();
        $operation = new Operation();
        $decorator($operation);
        self::assertEquals('create_user', $operation->getOperationId());
    }
}
