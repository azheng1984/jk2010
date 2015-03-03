<?php
namespace Hyperframework\Web;

use Hyperframework\Common\Config;
use Hyperframework\Common\FatalError;
use Hyperframework\Web\Test\TestCase as Base;

class DebuggerTest extends Base {
    public function testRun() {
        $this->expectOutputRegex('/^<!DOCTYPE html>/');
        Config::set('hyperframework.app_root_path', dirname(__DIR__));
        $debugger = new Debugger;
        $debugger->execute(new FatalError(E_ERROR, '', __FILE__, __LINE__));
    }
}
