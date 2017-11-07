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

namespace Ynlo\RestfulPlatformBundle\EventListener;

use JMS\Serializer\SerializationContext;
use JMS\Serializer\Serializer;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Ynlo\RestfulPlatformBundle\Api\RestApiInterface;
use Ynlo\RestfulPlatformBundle\Api\RestApiSpecification;
use Ynlo\RestfulPlatformBundle\Controller\RestApiControllerInterface;

class ApiRequestListener implements EventSubscriberInterface, ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents()
    {
        return [
            // KernelEvents::REQUEST => ['onKernelRequest'],
            KernelEvents::VIEW => ['onKernelView', 30],
            KernelEvents::CONTROLLER => ['onController'],
        ];
    }

    /**
     * @param FilterControllerEvent $event
     */
    public function onController(FilterControllerEvent $event)
    {
        $request = $event->getRequest();
        if ($apiCall = $request->get('_api')) {
            list($apiClass, $action) = explode(':', $apiCall);
            /** @var RestApiInterface $api */
            $pool = $this->container->get('restful_platform.api_pool');
            $api = $pool->getApiByClass($apiClass);
            if ($api instanceof ContainerAwareInterface) {
                $api->setContainer($this->container);
            }

            $api->setRequest($request);

            list($controller, $action) = $event->getController();
            if ($controller instanceof RestApiControllerInterface) {
                $controller->setApi($api);
            }
        }
    }

    /**
     * @param GetResponseForControllerResultEvent $event
     */
    public function onKernelView(GetResponseForControllerResultEvent $event)
    {
        $request = $event->getRequest();
        if ($apiCall = $request->get('_api')) {
            $controllerResult = $event->getControllerResult();
            if (!$controllerResult instanceof Response) {

                if (is_int($controllerResult)) {
                    $event->setResponse(new Response('', $controllerResult));

                    return;
                }

                $code = 200;
                $headers = [];
                if (is_array($controllerResult)) {
                    if (count($controllerResult) == 3) {
                        list($code, $controllerResult, $headers) = $controllerResult;
                    } else {
                        list($code, $controllerResult) = $controllerResult;
                    }
                }

                $routeName = $request->get('_route');

                $context = SerializationContext::create()->enableMaxDepthChecks();
                $groups = $this->getApiSpecification()->getResponseGroups($routeName, $code);
                if ($groups) {
                    $context->setGroups($groups);
                }

                $rawResponse = '';
                if ($controllerResult) {
                    $rawResponse = $this->getSerializer()->serialize($controllerResult, 'json', $context);
                }

                $newResponse = JsonResponse::fromJsonString($rawResponse, $code, $headers);

                $event->setResponse($newResponse);
            }
        }
    }

    /**
     * @return object|RestApiSpecification
     */
    protected function getApiSpecification()
    {
        return $this->container->get('restful_platform.api_specification');
    }

    /**
     * @return Serializer
     */
    protected function getSerializer()
    {
        return $this->container->get('serializer');
    }
}