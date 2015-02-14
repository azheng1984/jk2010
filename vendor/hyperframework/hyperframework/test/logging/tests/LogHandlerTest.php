<?php
namespace Hyperframework\Logging;

use Hyperframework\Common\Config;
use Hyperframework\Logging\Test\CustomLogFormatter;
use Hyperframework\Logging\Test\CustomLogWriter;
use Hyperframework\Test\TestCase as Base;

class LogHandlerTest extends Base {
    protected function tearDown() {
        Config::clear();
    }

    public function testHandleLog() {
        $logRecord = new LogRecord(LogLevel::ERROR, null);
        $formatter = $this->getMock('Hyperframework\Logging\LogFormatter');
        $formatter->expects($this->once())
            ->method('format')->with($this->identicalTo($logRecord))
            ->willReturn('text');
        $writer = $this->getMock('Hyperframework\Logging\LogWriter');
        $writer->expects($this->once())->method('write');
        $writer->method('write')->with($this->equalTo('text'));
        $handler = $this->getMockBuilder(
            'Hyperframework\Logging\LogHandler'
        )->setMethods(['getFormatter', 'getWriter'])->getMock();
        $handler->method('getFormatter')->willReturn($formatter);
        $handler->method('getWriter')->willReturn($writer);
        $handler->handle($logRecord);
    }

    public function testDefaultLogWriter() {
        $handler = new LogHandler;
        $this->assertTrue(
            $this->callProtectedMethod($handler, 'getWriter')
                instanceof LogWriter
        );
    }

    /**
     * @expectedException Hyperframework\Common\ClassNotFoundException
     */
    public function testInvalidLogWriter() {
        Config::set('hyperframework.logging.log_writer_class', 'Unknown');
        $handler = new LogHandler;
        $this->callProtectedMethod($handler, 'getWriter');
    }

    public function testCustomLogWriter() {
        Config::set(
            'hyperframework.logging.log_writer_class',
            'Hyperframework\Logging\Test\CustomLogWriter'
        );
        $handler = new LogHandler;
        $this->assertTrue(
            $this->callProtectedMethod($handler, 'getWriter')
                instanceof CustomLogWriter
        );
    }

    public function testDefaultLogFormatter() {
        $handler = new LogHandler;
        $this->assertTrue(
            $this->callProtectedMethod($handler, 'getFormatter')
                instanceof LogFormatter
        );
    }

    /**
     * @expectedException Hyperframework\Common\ClassNotFoundException
     */
    public function testInvalidLogFormatter() {
        Config::set('hyperframework.logging.log_formatter_class', 'Unknown');
        $handler = new LogHandler;
        $this->callProtectedMethod($handler, 'getFormatter');
    }

    public function testCustomLogFormatter() {
        Config::set(
            'hyperframework.logging.log_formatter_class',
            'Hyperframework\Logging\Test\CustomLogFormatter'
        );
        $handler = new LogHandler;
        $this->assertTrue(
            $this->callProtectedMethod($handler, 'getFormatter')
                instanceof CustomLogFormatter
        );
    }
}
