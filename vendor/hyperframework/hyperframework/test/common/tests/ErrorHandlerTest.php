<?php
namespace Hyperframework\Common;

use ReflectionFunction;
use Hyperframework\Common\Test\ErrorTriggeredErrorHandler;
use Hyperframework\Test\TestCase as Base;

class ErrorHandlerTest extends Base {
    private $errorReportingBitmask;
    private $shouldLogErrors;
    private $shouldDisplayErrors;
    private $errorLog;
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
        ini_set('log_errors', 1);
        ini_set('error_log', dirname(__DIR__) . '/data/tmp/log');
        ini_set('display_errors', 1);
        Config::set(
            'hyperframework.logging.log_path',
            dirname(__DIR__) . '/data/tmp/logger_log'
        );
    }

    private function bind() {
        $this->handler = new ErrorHandler;
        $this->callProtectedMethod($this->handler, 'registerErrorHandler');
    }

    protected function tearDown() {
        ini_set('xmlrpc_errors', 0);
        ini_set('html_errors', 0);
        ini_set('error_log', $this->errorLog);
        ini_set('log_errors', $this->shouldLogErrors);
        ini_set('display_errors', $this->shouldDisplayErrors);
        restore_error_handler();
        if (file_exists(dirname(__DIR__) . '/data/tmp/log')) {
            unlink(dirname(__DIR__) . '/data/tmp/log');
        }
        if (file_exists(dirname(__DIR__) . '/data/tmp/logger_log')) {
            unlink(dirname(__DIR__) . '/data/tmp/logger_log');
        }
        error_reporting($this->errorReportingBitmask);
        Config::clear();
    }

//    /**
//     * @expectedException Hyperframework\Common\ErrorException
//     */
//    public function testConvertErrorToException() {
//        $this->bind();
//        trigger_error('notice');
//    }

    public function testRegisterExceptionHandler() {
        $this->bind();
        $this->callProtectedMethod($this->handler, 'registerExceptionHandler');
        $handler = set_exception_handler(function() {});
        restore_exception_handler();
        restore_exception_handler();
        $reflection = new ReflectionFunction($handler);
        $this->assertSame($reflection->getClosureThis(), $this->handler);
    }

    public function testRegisterErrorHandler() {
        $this->bind();
        $this->callProtectedMethod($this->handler, 'registerErrorHandler');
        $handler = set_error_handler(function() {});
        restore_error_handler();
        restore_error_handler();
        $reflection = new ReflectionFunction($handler);
        $this->assertSame($reflection->getClosureThis(), $this->handler);
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
        $this->assertFalse(
            $this->callProtectedMethod($this->handler, 'isLoggerEnabled')
        );
        $this->assertTrue(
            $this->callProtectedMethod(
                $this->handler, 'isDefaultErrorLogEnabled'
            )
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
        $this->assertTrue(
            $this->callProtectedMethod($this->handler, 'isLoggerEnabled')
        );
        $this->assertFalse(
            $this->callProtectedMethod(
                $this->handler, 'isDefaultErrorLogEnabled'
            )
        );
        $this->expectOutputString(PHP_EOL . "Notice:  notice in "
            . __FILE__ . " on line " . (__LINE__ + 3) . PHP_EOL);
        $message = "PHP Notice:  notice in "
            . __FILE__ . " on line " . (__LINE__ + 1) . PHP_EOL;
        trigger_error('notice');
        $log = file_get_contents(dirname(__DIR__) . '/data/tmp/logger_log');
        $structuredMessage = "[NOTICE] PHP Notice: "
            . " notice in " . __FILE__ . " on line " . (__LINE__ - 3) . PHP_EOL;
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
        ini_set('display_errors', 0);
        $this->bind();
        Config::set('hyperframework.exit_function', function() {});
//      try {
          $this->methodForArgumentErrorTest();
//      } catch (ErrorException $e) {
           $e = $this->callProtectedMethod($this->handler, 'getError');
           $line = __LINE__ - 2;
           $file = __FILE__;
           var_dump($e);
//           $this->assertEquals($line, $e->getLine());
//           $this->assertEquals($file, $e->getFile());
//           return;
 //       }
 //       $this->fail();
    }

    public function testDefaultLogForArgumentError() {
        ini_set('display_errors', 0);
        $this->bind();
        Config::set(
            'hyperframework.error_handler.error_throwing_bitmask', 0
        );
        $line = $this->getMethodForArgumentErrorTestDefinitionLine();
        $suffix = 'PHP Warning:  Missing argument 1 for '
            . __CLASS__ . '::' . 'methodForArgumentErrorTest():'
            . __FILE__ . ':' . $line . ' in '
            . __FILE__ . ' on line ' . (__LINE__ + 1) . PHP_EOL;
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

    public function testTriggerErrorInErrorHandler() {
        Config::set(
            'hyperframework.error_handler.error_throwing_bitmask', 0
        );
        $this->handler = new ErrorTriggeredErrorHandler;
        $this->expectOutputString(PHP_EOL . "Notice: notice in " 
            . $this->handler->getFile(). " on line "
            . $this->handler->getErrorLine(). PHP_EOL);
        $this->callProtectedMethod($this->handler, 'registerErrorHandler');
        $this->callProtectedMethod($this->handler, 'registerExceptionHandler');
        trigger_error('notice');
    }

    public function
        testEnableFatalErrorAndCompileWarningReportingByShutdownHandler()
    {
        $this->bind();
        error_reporting(0);
        $this->callProtectedMethod($this->handler, 'handleShutdown');
        $this->assertEquals(error_reporting(), E_ERROR | E_PARSE | E_CORE_ERROR
            | E_COMPILE_ERROR | E_COMPILE_WARNING
        );
    }

    public function testLogErrorTraceByLogger() {
        //Config::set(
        //    'hyperframework.error_handler.logger.log_stack_trace', true
        //);
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
        //$this->assertTrue(strpos($log, PHP_EOL . "\tstack_trace:") !== false);
    }

    public function testWriteCustomLog() {
        Config::set(
            'hyperframework.error_handler.error_throwing_bitmask', 0
        );
        ini_set('display_errors', 0);
        $this->handler= $this->getMockBuilder('Hyperframework\Common\ErrorHandler')
            ->setMethods(['writeLog'])
            ->getMock();
        $this->handler->expects($this->once())
             ->method('writeLog');

        $this->callProtectedMethod($this->handler, 'registerErrorHandler');
        trigger_error('notice');
    }

    public function testWriteCustomErrorLog() {
        Config::set(
            'hyperframework.error_handler.error_throwing_bitmask', 0
        );
        ini_set('display_errors', 0);
        $this->handler = $this->getMockBuilder('Hyperframework\Common\ErrorHandler')
            ->setMethods(['writeDefaultErrorLog'])
            ->getMock();
        $this->handler->expects($this->once())
            ->method('writeDefaultErrorLog');
        $this->callProtectedMethod($this->handler, 'registerErrorHandler');
        trigger_error('notice');
    }

    public function testDisplayCustomErrorMessage() {
        Config::set(
            'hyperframework.error_handler.error_throwing_bitmask', 0
        );
        $this->handler = $this->getMockBuilder('Hyperframework\Common\ErrorHandler')
            ->setMethods(['displayError'])
            ->getMock();
        $this->handler->expects($this->once())
             ->method('displayError');
        $this->callProtectedMethod($this->handler, 'registerErrorHandler');
        trigger_error('notice');
    }

    public function testShouldDisplayErrors() {
        $this->bind();
        $this->assertTrue(
            $this->callProtectedMethod($this->handler, 'shouldDisplayErrors')
        );
    }

    public function testDisableDefaultErrorReporting() {
        $this->bind();
        $this->callProtectedMethod(
            $this->handler, 'disableDefaultErrorReporting'
        );
        $this->assertEquals(error_reporting(), E_COMPILE_WARNING);
        error_reporting(E_ALL & ~E_COMPILE_WARNING);
        $this->bind();
        $this->callProtectedMethod(
            $this->handler, 'disableDefaultErrorReporting'
        );
        $this->assertEquals(ini_get('display_errors'), '0');
        $this->assertEquals(ini_get('log_errors'), '0');
        $this->assertEquals(error_reporting(), E_ALL & ~E_COMPILE_WARNING);
    }

    public function testGetErrorReportingBitmask() {
        $this->bind();
        $this->assertEquals(
            $this->callProtectedMethod(
                $this->handler, 'getErrorReportingBitmask'
            ), 
            E_ALL
        );
    }

    public function testGetErrorAndIsError() {
        Config::set(
            'hyperframework.error_handler.error_throwing_bitmask', 0
        );
        $this->handler= $this->getMockBuilder(
            'Hyperframework\Common\Test\ErrorSpy')
            ->setMethods(['send'])
            ->getMock();
        $this->handler->expects($this->once())->method('send')->with(
            $this->isInstanceOf(__NAMESPACE__ . '\Error')
        );
        $this->callProtectedMethod($this->handler, 'registerErrorHandler');
        trigger_error('notice');
    }

    private function methodForArgumentErrorTest($param) {
    }

    private function getMethodForArgumentErrorTestDefinitionLine() {
        return __LINE__ - 4;
    }
}
