<?php
/*
 * LICENSE: This file is subject to the terms and conditions defined in
 * file 'LICENSE', which is part of this source code package.
 *
 * @copyright 2017 Copyright(c) - All rights reserved.
 *
 * @author YNLO-Ultratech Development Team <developer@ynloultratech.com>
 * @package restful-platform
 */

namespace Ynlo\RestfulPlatformBundle\Controller;

use JMS\Serializer\Serializer;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Debug\Exception\FlattenException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Log\DebugLoggerInterface;
use Ynlo\RestfulPlatformBundle\Exception\ApiError;

class ExceptionController extends Controller
{
    /**
     * Converts an Exception to a Response.
     *
     * @param Request              $request   The request
     * @param \Exception           $exception Exception instance
     * @param DebugLoggerInterface $logger    A DebugLoggerInterface instance
     *
     * @return Response
     *
     * @throws \InvalidArgumentException When the exception template does not exist
     */
    public function showAction(Request $request, \Exception $exception, DebugLoggerInterface $logger = null)
    {
        if ($exception instanceof HttpException) {
            $message = $exception->getMessage();
            $statusCode = $exception->getStatusCode();
            if (!$message) {
                $message = Response::$statusTexts[$statusCode] ?? null;
            }
            $exception = ApiError::create($statusCode, $message, $statusCode);
        }

        if (!$exception instanceof ApiError) {
            $code = 500;
            $content = null;

            if ($this->isDebug()) {
                $currentContent = $this->getAndCleanOutputBuffering($request->headers->get('X-Php-Ob-Level', -1));
                $content = $this->renderView(
                    '@Twig/Exception/exception_full.html.twig',
                    [
                        'status_code' => $code,
                        'status_text' => isset(Response::$statusTexts[$code]) ? Response::$statusTexts[$code] : '',
                        'exception' => FlattenException::create($exception),
                        'logger' => $logger,
                        'currentContent' => $currentContent,
                    ]
                );
            }

            return Response::create($content, $code, ['Content-Type' => 'text/html; charset=UTF-8']);
        }
        $statusCode = $exception->getStatusCode();

        $content = $this->get('serializer')->serialize($exception->getError(), 'json');

        return JsonResponse::fromJsonString($content, $statusCode);
    }


    /**
     * @return Serializer
     */
    protected function isDebug()
    {
        return $this->container->getParameter('kernel.debug');
    }

    /**
     * @param int $startObLevel
     *
     * @return string
     */
    protected function getAndCleanOutputBuffering($startObLevel)
    {
        if (ob_get_level() <= $startObLevel) {
            return '';
        }

        Response::closeOutputBuffers($startObLevel + 1, true);

        return ob_get_clean();
    }
}