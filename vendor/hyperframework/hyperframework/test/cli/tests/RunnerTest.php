<?php
namespace Hyperframework\Cli;

use Hyperframework\Common\Config;

class RunnerTest extends \PHPUnit_Framework_TestCase {
    public function testRun() {
        Runner::run('/home/az/quickquick/vendor/hyperframework/hyperframework/test/cli');
    }

    public function testInitializeAppRootPath() {
    }

    public function testCustomApp() {
    }

    public function testCustomAppClassNotFound() {
    }
}
