<?php
/**
 * LICENSE: This file is subject to the terms and conditions defined in
 * file 'LICENSE', which is part of this source code package.
 *
 * @copyright 2017 Copyright(c) - All rights reserved.
 * @author    YNLO-Ultratech Development Team <developer@ynloultratech.com>
 * @package   restful-platform
 */

namespace Ynlo\RestfulPlatformBundle\Tests\Routing;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Router;
use PHPUnit\Framework\TestCase;
use Ynlo\RestfulPlatformBundle\Routing\HateoasRouteGenerator;

class HateoasRouteGeneratorTest extends TestCase
{
    public function testGenerate()
    {
        $router = self::createMock(Router::class);
        $requestStack = self::createMock(RequestStack::class);
        $request = self::createMock(Request::class);

        $requestStack->expects(self::once())->method('getCurrentRequest')->willReturn($request);
        $request->expects(self::once())->method('get')->with('version')->willReturn('v2');

        $router->expects(self::once())->method('generate')
               ->with('user_list', ['version' => 'v2', 'q' => 'admin'], true);

        $generator = new HateoasRouteGenerator($router, $requestStack);
        $generator->generate('user_list', ['q' => 'admin'], true);
    }
}
