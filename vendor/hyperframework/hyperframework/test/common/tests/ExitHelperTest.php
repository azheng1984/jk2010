<?php
namespace Hyperframework\Common;

use Hyperframework\Common\Test\TestCase as Base;

class ExitHelperTest extends Base {
    public function testExitScript() {
        $isCalled = false;
        Config::set('hyperframework.exit_function',
            function() use (&$isCalled) {
                $isCalled = true;
            }
        );
        ExitHelper::exitScript();
        $this->assertTrue($isCalled);
    }

    /**
     * @expectedException Hyperframework\Common\ConfigException
     */
    public function testInvalidConfig() {
        Config::set('hyperframework.exit_function', true);
        ExitHelper::exitScript();
    }
}
