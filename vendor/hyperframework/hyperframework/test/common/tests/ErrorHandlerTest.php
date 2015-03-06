<?php
namespace Hyperframework\Common;

use ReflectionFunction;
use Exception;
use Hyperframework\Logging\Logger;
use Hyperframework\Test\TestCase as Base;

class ErrorHandlerTest extends Base {
    private $errorReportingBitmask;
    private $shouldLogErrors;
    private $shouldDisplayErrors;
    private $errorLog;
    private $errorPrependString;
    private $errorAppendString;
    private $handler;

    protected function setUp() {
        Config::set(
            'hyperframework.app_root_path',
            dirname(__DIR__)
        );
        $this->errorReportingBitmask = error_reporting();
        error_reporting(E_ALL);
        $this->shouldLogErrors = ini_get('log_errors');
        $this->errorLog = ini_get('error_log');
        $this->shouldDisplayErrors = ini_get('display_errors');
        $this->errorPrependString = ini_get('error_prepend_string');
        $this->errorAppendString = ini_get('error_append_string');
        ini_set('log_errors', 1);
        ini_set('error_log', dirname(__DIR__) . '/data/tmp/log');
        ini_set('display_errors', 1);
        ini_set('error_prepend_string', '');
        ini_set('error_append_string', '');
        Config::set(
            'hyperframework.logging.log_path',
            dirname(__DIR__) . '/data/tmp/logger_log'
        );
    }

    private function registerErrorHandler($handler = null) {
        if ($handler === null) {
            $this->handler = new ErrorHandler;
        } else {
            $this->handler = $handler;
        }
        $this->callPrivateMethod($this->handler, 'registerErrorHandler');
    }

    protected function tearDown() {
        ini_set('html_errors', 0);
        ini_set('error_log', $this->errorLog);
        ini_set('log_errors', $this->shouldLogErrors);
        ini_set('display_errors', $this->shouldDisplayErrors);
        ini_set('error_prepend_string', $this->errorPrependString);
        ini_set('error_append_string', $this->errorAppendString);
        if ($this->handler !== null) {
            restore_error_handler();
            $this->handler = null;
        }
        if (file_exists(dirname(__DIR__) . '/data/tmp/log')) {
            unlink(dirname(__DIR__) . '/data/tmp/log');
        }
        if (file_exists(dirname(__DIR__) . '/data/tmp/logger_log')) {
            unlink(dirname(__DIR__) . '/data/tmp/logger_log');
        }
        if (file_exists(dirname(__DIR__) . '/log/app.log')) {
            unlink(dirname(__DIR__) . '/log/app.log');
        }
        error_reporting($this->errorReportingBitmask);
        Logger::setLogHandler(null);
        Config::clear();
    }

    /**
     * @expectedException Hyperframework\Common\ErrorException
     */
    public function testConvertErrorToException() {
        Config::set(
            'hyperframework.error_handler.error_throwing_bitmask', E_ALL
        );
        $this->registerErrorHandler();
        trigger_error('notice');
    }

    private function handleError($handler = null, $error = null) {
        if ($handler === null) {
            $handler = new ErrorHandler;
        }
        if ($error === null) {
            $error = new Error(E_NOTICE, 'notice', __FILE__, 0);
        }
        $this->callPrivateMethod($handler, 'handle', [$error]);
    }

    public function testHandleFatalError() {
        $handler = $this->getMockBuilder('Hyperframework\Common\ErrorHandler')
            ->setMethods(['displayError', 'writeLog'])
            ->getMock();
        $handler->expects($this->once())
             ->method('displayError');
        $handler->expects($this->once())
             ->method('writeLog');
        $error = new Error(E_ERROR, '', __FILE__, 0);
        $this->callPrivateMethod($handler, 'handle', [$error]);
    }

    public function testHandleException() {
        $handler = $this->getMockBuilder('Hyperframework\Common\ErrorHandler')
            ->setMethods(['displayError'])
            ->getMock();
        $handler->expects($this->once())
            ->method('displayError');
        try {
            $this->callPrivateMethod(
                $handler, 'handleException', [new Exception]
            );
            $this->fail();
        } catch (Exception $e) {
        }
    }

    public function testRegisterExceptionHandler() {
        $handler = new ErrorHandler;
        $this->callPrivateMethod($handler, 'registerExceptionHandler');
        $callback = set_exception_handler(function() {});
        restore_exception_handler();
        restore_exception_handler();
        $reflection = new ReflectionFunction($callback);
        $this->assertSame($reflection->getClosureThis(), $handler);
    }

    public function testRegisterErrorHandler() {
        $handler = new ErrorHandler;
        $this->callPrivateMethod($handler, 'registerErrorHandler');
        $callback = set_error_handler(function() {});
        restore_error_handler();
        restore_error_handler();
        $reflection = new ReflectionFunction($callback);
        $this->assertSame($reflection->getClosureThis(), $handler);
    }

    public function testMaxLogLength() {
        Config::set(
            'hyperframework.error_handler.enable_logger', true
        );
        Config::set(
            'hyperframework.error_handler.max_log_length', 1
        );
        ini_set('display_errors', 0);
        $logHandler = $this->getMock('Hyperframework\Logging\LogHandler');
        Logger::setLogHandler($logHandler);
        $logHandler->expects($this->once())
            ->method('handle')->will($this->returnCallback(
                function ($logRecord) {
                    $this->assertSame(1, strlen($logRecord->getMessage()));
                }
            ));
        $this->callPrivateMethod(
            new ErrorHandler, 'handle', [new Exception]
        );
    }

    public function testWriteLogByLogger() {
        Config::set(
            'hyperframework.error_handler.enable_logger', true
        );
        //$this->expectOutputString(PHP_EOL . "Notice:  notice in "
        //    . __FILE__ . " on line 0" . PHP_EOL);
        $message = "PHP Notice:  notice in "
            . __FILE__ . " on line 0" . PHP_EOL;
        $this->handleError();
        $log = file_get_contents(dirname(__DIR__) . '/data/tmp/logger_log');
        $this->assertStringEndsWith(
            "[NOTICE] PHP Notice: "
                . " notice in " . __FILE__ . " on line 0" . PHP_EOL,
            $log
        );
        $this->assertFalse(
            file_exists(dirname(__DIR__) . '/data/tmp/log')
        );
    }

    public function testWriteLogUsingCustomLogger() {
        Config::set(
            'hyperframework.error_handler.enable_logger', true
        );
        Config::set(
            'hyperframework.error_handler.logger_class',
            'Hyperframework\Common\Test\Logger'
        );
        ini_set('display_errors', 0);
        $this->expectOutputString('Hyperframework\Common\Test\Logger::log');
        $this->handleError();
    }

    public function testLoggerIsDisabledByDefault() {
        //$this->expectOutputString(PHP_EOL . "Notice:  notice in "
        //    . __FILE__ . " on line 0" . PHP_EOL);
        $message = "PHP Notice: notice in "
            . __FILE__ . " on line 0" . PHP_EOL;
        $this->handleError();
        $this->assertFalse(
            file_exists(dirname(__DIR__) . '/data/tmp/logger_log')
        );
        //$this->assertFileExists(dirname(__DIR__) . '/data/tmp/log');
    }

    public function testThrowArgumentErrorException() {
        Config::set(
            'hyperframework.error_handler.error_throwing_bitmask', E_ALL
        );
        $this->registerErrorHandler();
        try {
            $function = function($arg) {};
            $function();
        } catch (ErrorException $e) {
            $line = __LINE__ - 2;
            $file = __FILE__;
            $this->assertEquals($e->getLine(), $line);
            $this->assertEquals($e->getFile(), $file);
            return;
        }
        $this->fail();
    }

    public function testGetError() {
        $handler = $this->getMockBuilder(
            'Hyperframework\Common\Test\ErrorSpy')
            ->setMethods(['send'])
            ->getMock();
        $handler->expects($this->once())->method('send')->with(
            $this->isInstanceOf(__NAMESPACE__ . '\Error')
        );
        $this->handleError($handler);
    }
}
