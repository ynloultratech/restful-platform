<?php
/**
 * LICENSE: This file is subject to the terms and conditions defined in
 * file 'LICENSE', which is part of this source code package.
 *
 * @copyright 2017 Copyright(c) - All rights reserved.
 * @author    YNLO-Ultratech Development Team <developer@ynloultratech.com>
 * @package   restful-platform
 */

namespace Ynlo\RestfulPlatformBundle\Exception;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Ynlo\RestfulPlatformBundle\Error\Error;
use Ynlo\RestfulPlatformBundle\Error\ValidationError;

class ApiError extends \RuntimeException
{
    /**
     * @var Error
     */
    protected $error;

    /**
     * @var int
     */
    protected $statusCode;

    /**
     * @param Error $error
     * @param int   $statusCode
     */
    public function __construct(Error $error, $statusCode)
    {
        parent::__construct($error->getMessage(), $error->getCode());

        $this->error = $error;
        $this->statusCode = $statusCode;
    }

    /**
     * @inheritDoc
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * @return Error
     */
    public function getError(): Error
    {
        return $this->error;
    }

    /**
     * @param int    $code
     * @param string $message
     * @param int    $statusCode
     *
     * @return ApiError
     */
    public static function create($code, $message, $statusCode)
    {
        return new ApiError(new Error($code, $message), $statusCode);
    }

    /**
     * @param int    $code
     * @param string $message
     *
     * @return ApiError
     */
    public static function badRequest($code, $message)
    {
        return new ApiError(new Error($code, $message), Response::HTTP_BAD_REQUEST);
    }

    /**
     * @param ConstraintViolationListInterface $violations
     *
     * @return ApiError
     */
    public static function validationError(ConstraintViolationListInterface $violations)
    {
        return new ApiError(new ValidationError($violations), Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}