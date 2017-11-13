<?php

namespace Ynlo\RestfulPlatformBundle\Tests\Error\ValidationError;

use Ynlo\RestfulPlatformBundle\Error\ValidationError\ValidationPropertyError;
use PHPUnit\Framework\TestCase;

class ValidationPropertyErrorTest extends TestCase
{
    public function testMessage()
    {
        $value = 'message';
        self::assertEquals($value, (new ValidationPropertyError())->setMessage($value)->getMessage());
    }

    public function testCode()
    {
        $value = '200';
        self::assertEquals($value, (new ValidationPropertyError())->setCode($value)->getCode());
    }

    public function testProperty()
    {
        $value = 'username';
        self::assertEquals($value, (new ValidationPropertyError())->setProperty($value)->getProperty());
    }

    public function testInvalidValue()
    {
        $value = 'none';
        self::assertEquals($value, (new ValidationPropertyError())->setInvalidValue($value)->getInvalidValue());
    }
}
