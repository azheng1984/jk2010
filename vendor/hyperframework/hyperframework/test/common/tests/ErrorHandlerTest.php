<?php
namespace Hyperframework\Common;

use Hyperframework\Common\Config;
use Hyperframework\Common\ArgumentErrorException;
use Hyperframework\Common\Test\ErrorTriggeredErrorHandler;

class ErrorHandlerTest extends \PHPUnit_Framework_TestCase {
    private $errorReportingBitmask;
    private $shouldLogErrors;
    private $shouldDisplayErrors;
    private $errorLog;
    private $handler;

    protected function setUp() {
        $this->errorReportingBitmask = error_reporting();
        error_reporting(E_ALL);
        $this->shouldLogErrors = ini_get('log_errors');
        $this->errorLog = ini_get('error_log');
        $this->shouldDisplayErrors = ini_get('display_errors');
        ini_set('log_errors', 1);
        ini_set('error_log', dirname(__DIR__) . '/data/tmp/log');
        ini_set('display_errors', 1);
        Config::set(
            'hyperframework.log_handler.log_path',
            dirname(__DIR__) . '/data/tmp/logger_log'
        );
    }

    private function bind() {
        $this->handler = new ErrorHandler;
        set_error_handler(
            [$this->handler, 'handleError'], error_reporting() 
        );
        set_exception_handler([$this->handler, 'handleException']);
    }

    protected function tearDown() {
        ini_set('xmlrpc_errors', 0);
        ini_set('html_errors', 0);
        ini_set('error_log', $this->errorLog);
        ini_set('log_errors', $this->shouldLogErrors);
        ini_set('display_errors', $this->shouldDisplayErrors);
        restore_error_handler();
        restore_exception_handler();
        if (file_exists(dirname(__DIR__) . '/data/tmp/log')) {
            unlink(dirname(__DIR__) . '/data/tmp/log');
        }
        if (file_exists(dirname(__DIR__) . '/data/tmp/logger_log')) {
            unlink(dirname(__DIR__) . '/data/tmp/logger_log');
        }
        error_reporting($this->errorReportingBitmask);
        Config::clear();
    }

    /**
     * @expectedException Hyperframework\Common\ErrorException
     */
    public function testErrorToException() {
        $this->bind();
        trigger_error('notice');
    }

    public function testDisplayDefaultErrorMessage() {
        $this->bind();
        Config::set(
            'hyperframework.error_handler.error_throwing_bitmask', 0
        );
        $this->expectOutputString(PHP_EOL . "Notice:  notice in "
            . __FILE__ . " on line " . (__LINE__ + 1) . PHP_EOL);
        trigger_error('notice');
    }

    public function testLogDefaultErrorMessage() {
        $this->bind();
        Config::set(
            'hyperframework.error_handler.error_throwing_bitmask', 0
        );
        $this->expectOutputString(PHP_EOL . "Notice:  notice in "
            . __FILE__ . " on line " . (__LINE__ + 3) . PHP_EOL);
        $message = "PHP Notice:  notice in "
            . __FILE__ . " on line " . (__LINE__ + 1) . PHP_EOL;
        trigger_error('notice');
        $log = file_get_contents(dirname(__DIR__) . '/data/tmp/log');
        $this->assertStringEndsWith($message, (string)$log);
    }

    public function testWriteLogByLogger() {
        Config::set(
            'hyperframework.error_handler.logger.enable', true
        );
        Config::set(
            'hyperframework.error_handler.error_throwing_bitmask', 0
        );
        $this->bind();
        $this->expectOutputString(PHP_EOL . "Notice:  notice in "
            . __FILE__ . " on line " . (__LINE__ + 3) . PHP_EOL);
        $message = "PHP Notice:  notice in "
            . __FILE__ . " on line " . (__LINE__ + 1) . PHP_EOL;
        trigger_error('notice');
        $log = file_get_contents(dirname(__DIR__) . '/data/tmp/logger_log');
        $structuredMessage = "| NOTICE | php_error | notice"
            . PHP_EOL . "\tfile: " . __FILE__
            . PHP_EOL . "\tline: " . (__LINE__ - 4)
            . PHP_EOL . "\ttype: E_USER_NOTICE" . PHP_EOL;
        $this->assertStringEndsWith($structuredMessage, $log);
        $this->assertFalse(
            file_exists(dirname(__DIR__) . '/data/tmp/log')
        );
    }

    public function testLoggerIsDisabledByDefault() {
        Config::set(
            'hyperframework.error_handler.error_throwing_bitmask', 0
        );
        $this->bind();
        $this->expectOutputString(PHP_EOL . "Notice:  notice in "
            . __FILE__ . " on line " . (__LINE__ + 3) . PHP_EOL);
        $message = "PHP Notice: notice in "
            . __FILE__ . " on line " . (__LINE__ + 1) . PHP_EOL;
        trigger_error('notice');
        $this->assertFalse(
            file_exists(dirname(__DIR__) . '/data/tmp/logger_log')
        );
        $this->assertFileExists(dirname(__DIR__) . '/data/tmp/log');
    }

    public function testThrowArgumentErrorException() {
        $this->bind();
        try {
            $this->methodForArgumentErrorTest();
        } catch (ArgumentErrorException $e) {
            $line = __LINE__ - 2;
            $file = __FILE__;
            $this->assertEquals($e->getLine(), $line);
            $this->assertEquals($e->getFile(), $file);
            $this->assertEquals(
                $e->getFunctionDefinitionLine(),
                $this->getMethodForArgumentErrorTestDefinitionLine()
            );
            $this->assertEquals($e->getFunctionDefinitionFile(), $file);
            return;
        }
        $this->fail();
    }

    public function testDefaultLogForArgumentError() {
        ini_set('display_errors', 0);
        $this->bind();
        Config::set(
            'hyperframework.error_handler.error_throwing_bitmask', 0
        );
        $suffix = 'PHP Warning:  Missing argument 1 for '
            . __CLASS__ . '::' . 'methodForArgumentErrorTest() called in '
            . __FILE__ . ' on line ' . (__LINE__ + 3) . ' and defined in '
            . __FILE__ . " on line "
            . $this->getMethodForArgumentErrorTestDefinitionLine() . PHP_EOL;
        $this->methodForArgumentErrorTest();
        $log = file_get_contents(dirname(__DIR__) . '/data/tmp/log');
        $this->assertStringEndsWith($suffix, (string)$log);
    }

    public function testDisplayHtmlErrorMessage() {
        ini_set('html_errors', 1);
        $this->bind();
        Config::set(
            'hyperframework.error_handler.error_throwing_bitmask', 0
        );
        $this->expectOutputString("<br />"
            . PHP_EOL . "<b>Notice</b>:  notice in <b>"
            . __FILE__ . "</b> on line <b>" . (__LINE__ + 2)
            . '</b><br />' . PHP_EOL);
        trigger_error('notice');
    }

    public function testTriggerErrorOnErrorHandler() {
        Config::set(
            'hyperframework.error_handler.error_throwing_bitmask', 0
        );
        $this->handler = new ErrorTriggeredErrorHandler;
        $this->expectOutputString(PHP_EOL . "Notice: notice in " 
            . $this->handler->getFile(). " on line "
            . $this->handler->getErrorLine(). PHP_EOL);
        set_error_handler(
            [$this->handler, 'handleError'], error_reporting() 
        );
        set_exception_handler([$this->handler, 'handleException']);
        trigger_error('notice');
    }

    public function
        testEnableFatalErrorAndCompileWarningReportingByFatalErrorHandler()
    {
        $this->bind();
        error_reporting(0);
        $this->handler->handleFatalError();
        $this->assertEquals(error_reporting(), E_ERROR | E_PARSE | E_CORE_ERROR
            | E_COMPILE_ERROR | E_COMPILE_WARNING
        );
    }

    public function testLogErrorTraceByLogger() {
        Config::set(
            'hyperframework.error_handler.logger.log_stack_trace', true
        );
        Config::set(
            'hyperframework.error_handler.logger.enable', true
        );
        Config::set(
            'hyperframework.error_handler.error_throwing_bitmask', 0
        );
        $this->bind();
        $this->expectOutputString(PHP_EOL . "Notice:  notice in "
            . __FILE__ . " on line " . (__LINE__ + 1) . PHP_EOL);
        trigger_error('notice');
        $log = file_get_contents(dirname(__DIR__) . '/data/tmp/logger_log');
        $this->assertTrue(strpos($log, PHP_EOL . "\tstack_trace:") !== false);
    }

    private function methodForArgumentErrorTest($param) {
    }

    private function getMethodForArgumentErrorTestDefinitionLine() {
        return __LINE__ - 4;
    }
}
