<?php
namespace Hyperframework\Web;

use Hyperframework\Common\Config;
use Hyperframework\Logging\Logger;
use Hyperframework\Web\ResponseHeader;
use Hyperframework\Web\Test\TestCase as Base;

class ErrorHandlerTest extends Base {
    protected function setUp() {
        parent::setUp();
        $this->errorReportingBitmask = error_reporting();
        error_reporting(E_ALL);
        $this->shouldLogErrors = ini_get('log_errors');
        $this->errorLog = ini_get('error_log');
        $this->shouldDisplayErrors = ini_get('display_errors');
        ini_set('display_errors', 1);
        ini_set('log_errors', 1);
        ini_set('error_log', dirname(__DIR__) . '/data/tmp/log');
        Config::set(
            'hyperframework.logging.log_path',
            dirname(__DIR__) . '/data/tmp/logger_log'
        );
        $engine = $this->getMock(
            'Hyperframework\Web\ResponseHeaderEngine'
        );
        $engine->method('isSent')->willReturn(
            false
        );
        ResponseHeader::setEngine($engine);
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
        if (file_exists(dirname(__DIR__) . '/log/app.log')) {
            unlink(dirname(__DIR__) . '/log/app.log');
        }
        error_reporting($this->errorReportingBitmask);
        Logger::setLogHandler(null);
        ResponseHeader::setEngine(null);
        parent::tearDown();
    }

    public function testDisplayErrorUsingErrorView() {
        $this->expectOutputString('500 Internal Server Error');
        $handler = new ErrorHandler;
        $this->callProtectedMethod($handler, 'handle');
    }

    public function testHandle() {
        $engine = $this->getMockBuilder('Hyperframework\Web\ErrorHandler')
            ->setMethods(['writeLog', 'displayError'])->getMock();
        $engine->expects($this->once())->method('writeLog');
        $engine->expects($this->once())->method('displayError');
        $this->callProtectedMethod($engine, 'handle');
    }

    public function testDisplayErrorUsingDebugger() {
        Config::set('hyperframework.error_handler.debug', true);
        $engine = $this->getMockBuilder('Hyperframework\Web\ErrorHandler')
            ->setMethods(['executeDebugger'])->getMock();
        $engine->expects($this->once())->method('executeDebugger');
        $this->callProtectedMethod($engine, 'displayError');
        ob_end_flush();
    }

    public function testRenderErrorView() {
        $engine = $this->getMockBuilder('Hyperframework\Web\ErrorHandler')
            ->setMethods(['renderErrorView'])->getMock();
        $engine->expects($this->once())->method('renderErrorView');
        $this->callProtectedMethod($engine, 'displayError');
    }

    public function testRenderCustomErrorView() {
        $this->expectOutputString(
            '500, Internal Server Error, '
                . 'Hyperframework\Web\Test\ErrorView::render'
        );
        Config::set(
            'hyperframework.web.error_view.class',
            'Hyperframework\Web\Test\ErrorView'
        );
        $handler = new ErrorHandler;
        $this->callProtectedMethod($handler, 'renderErrorView');
    }

    public function testRenderCustomErrorViewForHttpException() {
        $this->expectOutputString(
            '404, Not Found, Hyperframework\Web\Test\ErrorView::render'
        );
        Config::set(
            'hyperframework.web.error_view.class',
            'Hyperframework\Web\Test\ErrorView'
        );
        $handler = $this->getMockBuilder('Hyperframework\Web\ErrorHandler')
            ->setMethods(['getError'])->getMock();
        $handler->method('getError')->willReturn(new NotFoundException);
        $this->callProtectedMethod($handler, 'renderErrorView');
    }


    public function testIgnoreHttpExceptionLog() {
        Config::set(
            'hyperframework.error_handler.enable_logger', true
        );
        $error = new NotFoundException;
        $handler = $this
            ->getMockBuilder('Hyperframework\Web\ErrorHandler')
            ->setMethods(['getError'])->getMock();
        $handler->method('getError')->willReturn($error);
        $this->callProtectedMethod($handler, 'writeLog');
        $this->assertFalse(
            file_exists(dirname(__DIR__) . '/data/tmp/logger_log')
        );
    }

    public function testGetOutputBuffer() {
        ob_start();
        echo 'value';
        $handler = new ErrorHandler;
        $this->assertSame(
            'value', $this->callProtectedMethod($handler, 'getOutputBuffer')
        );
        ob_end_clean();
    }

    public function testFlushInnerOutputBuffer() {
        Config::set('hyperframework.error_handler.debug', true);
        $engine = $this->getMockBuilder('Hyperframework\Web\ErrorHandler')
            ->setMethods(['executeDebugger'])->getMock();
        $engine->expects($this->once())
            ->method('executeDebugger')->with(null, 'content');
        echo 'content';
        ob_start();
        $this->callProtectedMethod($engine, 'displayError');
        ob_end_flush();
    }

    public function testDeleteOutputBuffer() {
        $level = ob_get_level();
        $engine = $this->getMockBuilder('Hyperframework\Web\ErrorHandler')
            ->setMethods(['renderErrorView'])->getMock();
        ob_start();
        echo 'content';
        $this->callProtectedMethod($engine, 'displayError');
        $this->assertSame($level, ob_get_level());
    }

    public function testExecuteDebugger() {
        $this->expectOutputString('Hyperframework\Web\Test\Debugger::execute');
        Config::set('hyperframework.error_handler.debug', true);
        Config::set(
            'hyperframework.error_handler.debugger_class',
            'Hyperframework\Web\Test\Debugger'
        );
        $handler = new ErrorHandler;
        $this->callProtectedMethod($handler, 'executeDebugger', [null, null]);
        ob_end_flush();
    }

    public function testRewriteHttpHeaders() {
        $engine = $this->getMock(
            'Hyperframework\Web\ResponseHeaderEngine'
        );
        $engine->expects($this->once())->method('removeAllHeaders');
        $engine->expects($this->once())->method('setHeader')
            ->with('HTTP/1.1 500 Internal Server Error');
        $engine->method('isSent')->willReturn(false);
        ResponseHeader::setEngine($engine);
        $handler = $this->getMockBuilder('Hyperframework\Web\ErrorHandler')
            ->setMethods(['renderErrorView'])->getMock();
        $this->callProtectedMethod($handler, 'displayError');
    }

    public function testRewriteHttpHeadersForHttpException() {
        $engine = $this->getMock(
            'Hyperframework\Web\ResponseHeaderEngine'
        );
        $engine->expects($this->once())->method('removeAllHeaders');
        $engine->expects($this->once())->method('setHeader')
            ->with('HTTP/1.1 404 Not Found');
        $engine->method('isSent')->willReturn(false);
        ResponseHeader::setEngine($engine);
        $handler = $this->getMockBuilder('Hyperframework\Web\ErrorHandler')
            ->setMethods(['renderErrorView', 'getError'])->getMock();
        $handler->method('getError')->willReturn(new NotFoundException);
        $this->callProtectedMethod($handler, 'displayError');
    }
}
