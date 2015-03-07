<?php
namespace Hyperframework\Web;

use Hyperframework\Common\Config;
use Hyperframework\Logging\Logger;
use Hyperframework\Web\ResponseHeaderHelper;
use Hyperframework\Web\Test\TestCase as Base;

class ErrorHandlerTest extends Base {
    protected function setUp() {
        parent::setUp();
        $this->errorReportingBitmask = error_reporting();
        error_reporting(E_ALL);
        $this->shouldLogErrors = ini_get('log_errors');
        $this->errorLog = ini_get('error_log');
        $this->shouldDisplayErrors = ini_get('display_errors');
        ini_set('log_errors', 1);
        ini_set('error_log', dirname(__DIR__) . '/data/tmp/log');
        ini_set('display_errors', 0);
        Config::set(
            'hyperframework.logging.log_path',
            dirname(__DIR__) . '/data/tmp/logger_log'
        );
    }

    private function bind() {
        $this->handler = new ErrorHandler;
        $this->callPrivateMethod($this->handler, 'registerErrorHandler');
    }

    protected function tearDown() {
        ini_set('xmlrpc_errors', 0);
        ini_set('html_errors', 0);
        ini_set('error_log', $this->errorLog);
        ini_set('log_errors', $this->shouldLogErrors);
        ini_set('display_errors', $this->shouldDisplayErrors);
        restore_error_handler();
//        if (file_exists(dirname(__DIR__) . '/data/tmp/log')) {
//            unlink(dirname(__DIR__) . '/data/tmp/log');
//        }
        if (file_exists(dirname(__DIR__) . '/data/tmp/logger_log')) {
            unlink(dirname(__DIR__) . '/data/tmp/logger_log');
        }
        if (file_exists(dirname(__DIR__) . '/log/app.log')) {
            unlink(dirname(__DIR__) . '/log/app.log');
        }
        error_reporting($this->errorReportingBitmask);
        Logger::setLogHandler(null);
        ResponseHeaderHelper::setEngine(null);
        parent::tearDown();
    }

    public function testRenderErrorView() {
        $this->expectOutputString('500 Internal Server Error');
        $engine = $this->getMock('Hyperframework\Web\ResponseHeaderHelperEngine');
        $engine->expects($this->once())->method('isSent')->willReturn(
            false
        );
        ResponseHeaderHelper::setEngine($engine);
        $handler = new ErrorHandler;
        $this->callProtectedMethod($handler, 'handle');
    }

    public function testRenderErrorViewForHttpException() {
    }

    public function testRenderCustomErrorView() {
    }

    public function testIgnoreHttpExceptionLog() {
    }

    public function testGetOutputBufferForDebugging() {
    }

    public function testDeleteOutputBufferForErrorView() {
    }

    public function testDecodeGzipOutputBuffer() {
    }

    public function testDecodeDeflateOutputBuffer() {
    }

    public function testConvertOutputBufferCharset() {
    }

    public function testExecuteDebugger() {
    }

    public function testExecuteCustomDebugger() {
    }

    public function testRewriteHttpHeaders() {
    }

    public function testRewriteHttpHeadersForHttpException() {
    }

    public function testDisplayFatalErrorUsingDebugger() {
    }

    public function testDisplayFatalErrorUsingDefaultDisplayErrorMethod() {
    }

    public function testDisplayFatalErrorUsingErrorView() {
    }
}
