<?php
namespace Hyperframework\Common;

use Hyperframework\Common\Config;

class ErrorHandlerTest extends \PHPUnit_Framework_TestCase {
    private $errorReportingBitmask;
    private $shouldLogErrors;
    private $shouldDisplayErrors;
    private $errorLog;

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
        $handler = new ErrorHandler;
        set_error_handler(
            [$handler, 'handleError'], $this->errorReportingBitmask
        );
        set_exception_handler([$handler, 'handleException']);
    }

    protected function tearDown() {
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
        $this->assertTrue(strlen($log) > 0);
        $this->assertFalse(
            file_exists(dirname(__DIR__) . '/data/tmp/log')
        );
    }

    public function testDisableLoggerByDefault() {
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
        $this->assertTrue(
            file_exists(dirname(__DIR__) . '/data/tmp/log')
        );
    }
}
