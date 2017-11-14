<?php
/**
 * LICENSE: This file is subject to the terms and conditions defined in
 * file 'LICENSE', which is part of this source code package.
 *
 * @copyright 2017 Copyright(c) - All rights reserved.
 * @author    YNLO-Ultratech Development Team <developer@ynloultratech.com>
 * @package   restful-platform
 */

namespace Ynlo\RestfulPlatformBundle\Tests\Swagger\Model;

use Ynlo\RestfulPlatformBundle\Swagger\Model\Parameter;
use PHPUnit\Framework\TestCase;

class ParameterTest extends TestCase
{
    public function testConstructor()
    {
        $param = new Parameter('name', Parameter::IN_QUERY);
        self::assertEquals('name', $param->getName());
        self::assertEquals(Parameter::IN_QUERY, $param->getIn());
        self::assertFalse($param->isRequired());

        self::assertFalse((new Parameter('name', Parameter::IN_HEADER))->isRequired());
        self::assertFalse((new Parameter('name', Parameter::IN_FORM))->isRequired());
        self::assertTrue((new Parameter('name', Parameter::IN_PATH))->isRequired());
        self::assertTrue((new Parameter('name', Parameter::IN_BODY))->isRequired());
    }

    public function testIn()
    {
        $value = Parameter::IN_FORM;
        self::assertEquals($value, (new Parameter('name'))->setIn($value)->getIn());
    }

    public function testRequired()
    {
        $value = true;
        self::assertEquals($value, (new Parameter('name'))->setRequired($value)->isRequired());
    }
}
