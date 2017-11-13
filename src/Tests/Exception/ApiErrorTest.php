<?php

namespace Ynlo\RestfulPlatformBundle\Tests\Exception;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Ynlo\RestfulPlatformBundle\Error\Error;
use Ynlo\RestfulPlatformBundle\Exception\ApiError;
use PHPUnit\Framework\TestCase;

class ApiErrorTest extends TestCase
{
    public function testConstructor()
    {
        $error = new Error(1001, 'Something is wrong');
        $exception = new ApiError($error, Response::HTTP_NOT_ACCEPTABLE);

        self::assertEquals($error, $exception->getError());
        self::assertEquals(Response::HTTP_NOT_ACCEPTABLE, $exception->getStatusCode());
        self::assertEquals(1001, $exception->getCode());
        self::assertEquals('Something is wrong', $exception->getMessage());
    }

    public function testCreate()
    {
        $exception = ApiError::create(1001, 'Record not found', Response::HTTP_NOT_FOUND);

        self::assertEquals(Response::HTTP_NOT_FOUND, $exception->getStatusCode());
        self::assertEquals(1001, $exception->getCode());
        self::assertEquals('Record not found', $exception->getMessage());
    }

    public function testBadRequest()
    {
        $exception = ApiError::badRequest(99, 'The username is not valid');

        self::assertEquals(Response::HTTP_BAD_REQUEST, $exception->getStatusCode());
        self::assertEquals(99, $exception->getCode());
        self::assertEquals('The username is not valid', $exception->getMessage());
    }

    public function testValidationError()
    {
        $violations = new ConstraintViolationList();
        $exception = ApiError::validationError($violations);

        self::assertEquals(Response::HTTP_UNPROCESSABLE_ENTITY, $exception->getStatusCode());
        self::assertEquals('Validation failed with 0 error(s).', $exception->getMessage());
    }
}
