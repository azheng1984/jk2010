<?php
namespace Hyperframework\Logging;

use Hyperframework\Test\TestCase as Base;

class LogFormatterTest extends Base {
    private $time;

    protected function setUp() {
        $this->time = time();
    }

    public function testLogWithMessage() {
        $this->assertSame(
            $this->getLogPrefix() . ' message' . PHP_EOL,
            $this->getFormattedText(true)
        );
    }

    public function testLogWithoutMessage() {
        $this->assertSame(
            $this->getLogPrefix() . PHP_EOL,
            $this->getFormattedText(false)
        );
    }

    private function getLogPrefix() {
        return date("Y-m-d H:i:s", $this->time) . ' [ERROR]';
    }

    private function getFormattedText($hasMessage) {
        $formatter = new LogFormatter;
        $data = ['time' => $this->time, 'level' => 'ERROR'];
        if ($hasMessage) {
            $data['message'] = 'message';
        }
        $record = new LogRecord($data);
        return $formatter->format($record);
    }
}
