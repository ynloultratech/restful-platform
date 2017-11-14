<?php
/**
 * LICENSE: This file is subject to the terms and conditions defined in
 * file 'LICENSE', which is part of this source code package.
 *
 * @copyright 2017 Copyright(c) - All rights reserved.
 * @author    YNLO-Ultratech Development Team <developer@ynloultratech.com>
 * @package   restful-platform
 */

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
