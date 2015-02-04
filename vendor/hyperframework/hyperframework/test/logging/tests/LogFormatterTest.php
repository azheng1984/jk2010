<?php
namespace Hyperframework\Logging;

use Hyperframework\Test\TestCase as Base;

class LogFormatterTest extends Base {
    public function testMessage() {
        $formatter = new LogFormatter;
        $time = time();
        $this->assertSame(
            date("Y-m-d H:i:s", $time) . ' | ERROR || message' . PHP_EOL,
            $formatter->format(
                'ERROR', ['message' => 'message', 'time' => $time]
            )
        );
    }

    public function testLogWithoutMessage() {
        $formatter = new LogFormatter;
        $time = time();
        $this->assertSame(
            date("Y-m-d H:i:s", $time) . ' | ERROR' . PHP_EOL,
            $formatter->format('ERROR', ['time' => $time])
        );
    }

    public function testName() {
        $formatter = new LogFormatter;
        $time = time();
        $this->assertSame(
            date("Y-m-d H:i:s", $time) . ' | ERROR | name' . PHP_EOL,
            $formatter->format('ERROR', ['time' => $time, 'name' => 'name'])
        );
    }
}
