<?php
namespace Hyperframework\Common;

use Hyperframework\Common\Test\TestCase as Base;
use Hyperframework\Common\Test\App;

class AppTest extends Base {
    public function testConstruct() {
        $app = $this->getMockBuilder('Hyperframework\Common\Test\App')
            ->setMethods(['initializeConfig', 'initializeErrorHandler'])
            ->disableOriginalConstructor()->getMock();
        $app->expects($this->once())->method('initializeConfig');
        $app->expects($this->once())->method('initializeErrorHandler');
        $app->__construct('/path');
        $this->assertSame('/path', Config::getAppRootPath());
    }

    public function testDisableInitializeConfig() {
        Config::set('hyperframework.initialize_config', false);
        $isCalled = false;
        $app = $this->getMockBuilder('Hyperframework\Common\Test\App')
            ->setMethods(['initializeConfig', 'initializeErrorHandler'])
            ->disableOriginalConstructor()->getMock();
        $app->method('initializeConfig')->will($this->returnCallback(
            function() use (&$isCalled) {
                $isCalled = true;
            }
        ));
        $app->__construct('/path');
        $this->assertFalse($isCalled);
    }

    public function testDisableInitializeErrorHandler() {
        Config::set('hyperframework.initialize_error_handler', false);
        $isCalled = false;
        $app = $this->getMockBuilder('Hyperframework\Common\Test\App')
            ->setMethods(['initializeConfig', 'initializeErrorHandler'])
            ->disableOriginalConstructor()->getMock();
        $app->method('initializeErrorHandler')->will($this->returnCallback(
            function() use (&$isCalled) {
                $isCalled = true;
            }
        ));
        $app->__construct('/path');
        $this->assertFalse($isCalled);
    }

    public function testQuit() {
        Config::set('hyperframework.initialize_config', false);
        Config::set('hyperframework.initialize_error_handler', false);
        $app = $this->getMockBuilder('Hyperframework\Common\Test\App')
            ->setMethods(['finalize'])
            ->setConstructorArgs(['/path'])->getMock();
        $app->expects($this->once())->method('finalize');
        $isExitCalled = false;
        Config::set('hyperframework.exit_function',
            function() use (&$isExitCalled) {
                $isExitCalled = true;
            }
        );
        $app->quit();
        $this->assertTrue($isExitCalled);
    }

    /**
     * @expectedException Hyperframework\Common\InvalidOperationException
     */
    public function testQuitTwice() {
        Config::set('hyperframework.initialize_config', false);
        Config::set('hyperframework.initialize_error_handler', false);
        Config::set('hyperframework.exit_function', function() {});
        $app = new App('/path');
        $app->quit();
        $app->quit();
    }

    public function testInitializeConfig() {
        Config::set('hyperframework.initialize_error_handler', false);
        $_ENV['HYPERFRAMEWORK_ENV'] = 'dev';
        $app = new App(dirname(__DIR__));
        $this->assertSame('value', Config::get('key'));
        $this->assertSame('dev_value', Config::get('dev_key'));
    }

    public function testInitializeConfigWithoutEnvConfigFile() {
        Config::set('hyperframework.initialize_error_handler', false);
        $_ENV['HYPERFRAMEWORK_ENV'] = 'test';
        $app = new App(dirname(__DIR__));
        $this->assertSame('value', Config::get('key'));
    }

    public function testInitializeErrorHandler() {
        $this->expectOutputString(
            'Hyperframework\Common\Test\ErrorHandler::run'
        );
        $app = $this->getMockBuilder('Hyperframework\Common\Test\App')
            ->disableOriginalConstructor()->getMock();
        $this->callProtectedMethod(
            $app,
            'initializeErrorHandler',
            ['Hyperframework\Common\Test\ErrorHandler']
        );
    }

    public function testInitializeErrorHandlerByConfig() {
        Config::set(
            'hyperframework.error_handler.class',
            'Hyperframework\Common\Test\ErrorHandler'
        );
        $this->expectOutputString(
            'Hyperframework\Common\Test\ErrorHandler::run'
        );
        new App(dirname(__DIR__));
    }

    /**
     * @expectedException Hyperframework\Common\ClassNotFoundException
     */
    public function testInitializeErrorHandlerByInvalidConfig() {
        Config::set('hyperframework.error_handler.class', 'Unknown');
        new App(dirname(__DIR__));
    }

    public function mockApp() {
        $app = $this->getMockBuilder('Hyperframework\Common\Test\App')
            ->setMethods(
                ['initializeConfig', 'initializeErrorHandler', 'finalize', 'quit']
            )
            ->disableOriginalConstructor()->getMock();
    }
}
