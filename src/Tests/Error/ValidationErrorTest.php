<?php

namespace Ynlo\RestfulPlatformBundle\Tests\Error;

use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Ynlo\RestfulPlatformBundle\Tests\Fixtures\Model\User;
use Ynlo\RestfulPlatformBundle\Error\ValidationError;
use PHPUnit\Framework\TestCase;

class ValidationErrorTest extends TestCase
{
    public function testConstructor()
    {
        $violations = new ConstraintViolationList();
        $violation = new ConstraintViolation('Invalid username', null, [], new User(), 'username', null);
        $violation2 = new ConstraintViolation('Invalid property', null, [], new User(), 'someProperty', null);
        $violations->add($violation);
        $violations->add($violation2);

        $validationError = new ValidationError($violations);

        self::assertEquals('Invalid username', $validationError->getErrors()[0]->getMessage());
        self::assertEquals('username', $validationError->getErrors()[0]->getProperty());

        self::assertEquals('Invalid property', $validationError->getErrors()[1]->getMessage());
        self::assertEquals('someProperty', $validationError->getErrors()[1]->getProperty());
    }
}
