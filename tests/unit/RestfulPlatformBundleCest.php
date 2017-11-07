<?php

use Ynlo\RestfulPlatformBundle\RestfulPlatformBundle;

class RestfulPlatformBundleCest
{
    public function testBundleConstructor(UnitTester $I)
    {
        $bundle = new RestfulPlatformBundle();
        $I->assertEquals('RestfulPlatformBundle', $bundle->getName());
    }
}
