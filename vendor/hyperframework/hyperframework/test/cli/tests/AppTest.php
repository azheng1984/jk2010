<?php
namespace Hyperframework\Cli;

use Hyperframework\Common\Config;

class AppTest extends \PHPUnit_Framework_TestCase {
    private $App;

    protected function setUp() {
        Config::set(
            'hyperframework.app_root_path',
            '/home/az/quickquick/vendor/hyperframework/hyperframework/test/cli'
        );
        Config::set(
            'hyperframework.app_root_namespace', 'Hyperframework\Cli\Test'
        );
        parent::setUp();
        $_SERVER['argv'] = [];
        $_SERVER['argc'] = 0;
        $this->App = new App;
    }

    protected function tearDown() {
        parent::tearDown();
    }

    public function __construct() {
    }

    public function testRun() {
        $this->expectOutputString('hi');
        $this->App->run();
    }
}
