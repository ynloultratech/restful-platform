<?php

namespace Tests\Swagger\Specification\Operation;

use Ynlo\RestfulPlatformBundle\Swagger\Model\Operation;
use Ynlo\RestfulPlatformBundle\Swagger\Model\Response;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Common\CallableSpec;
use PHPUnit\Framework\TestCase;
use Ynlo\RestfulPlatformBundle\Swagger\Specification\Operation\ResponseSpec;

class ResponseSpecTest extends TestCase
{
    public function testDecorator()
    {
        $success = new CallableSpec(
            function (Response $response) {
                $response->setDescription('Success!!!');
            }
        );

        $spec = new ResponseSpec(200, [$success]);
        $decorator = $spec->getDecorator();

        $operation = new Operation();
        $decorator($operation);

        //use the same response to avoid duplicates
        $decorator($operation);

        self::assertEquals(1, $operation->getResponses()->count());

        /** @var Response $response */
        $response = $operation->getResponses()->get(200);
        self::assertEquals('Success!!!', $response->getDescription());
    }
}
