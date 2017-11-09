<?php

namespace Tests\Swagger\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Ynlo\RestfulPlatformBundle\Swagger\Model\Operation;
use PHPUnit\Framework\TestCase;

class OperationTest extends TestCase
{
    public function testOperationId()
    {
        $value = 'operationId';
        self::assertEquals($value, (new Operation())->setOperationId($value)->getOperationId());
    }

    public function testTags()
    {
        $value = new ArrayCollection();
        self::assertEquals($value, (new Operation())->setTags($value)->getTags());
        self::assertNotNull($value, (new Operation())->getTags());
    }

    public function testSummary()
    {
        $value = 'summary';
        self::assertEquals($value, (new Operation())->setSummary($value)->getSummary());
    }

    public function testParameters()
    {
        $value = new ArrayCollection();
        self::assertEquals($value, (new Operation())->setParameters($value)->getParameters());
        self::assertNotNull($value, (new Operation())->getParameters());
    }

    public function testResponses()
    {
        $value = new ArrayCollection(['200' => 'success']);
        self::assertEquals($value, (new Operation())->setResponses($value)->getResponses());
        self::assertEquals('success', (new Operation())->setResponses($value)->getResponse(200));
        self::assertNotNull($value, (new Operation())->getResponses());
    }
}
