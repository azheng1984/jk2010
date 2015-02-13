<?php
namespace Hyperframework\Logging;

use DateTime;
use Hyperframework\Test\TestCase as Base;
use Hyperframework\Common\Config;

class LogRecordTest extends Base {
    public function testDefaultTime() {
        $record = new LogRecord(
            ['level' => 'ERROR', 'message' => 'message']
        );
        $this->assertTrue($record->getTime() instanceof DateTime);
    }

    public function testIntegerTimeForTimeOption() {
        $time = time();
        $record = new LogRecord(
            ['level' => 'ERROR', 'time' => $time]
        );
        $this->assertSame(
            date('Y-m-d H:i:s', $time),
            $record->getTime()->format('Y-m-d H:i:s')
        );
    }

    public function testDateTimeForTimeOption() {
        $time = new DateTime;
        $record = new LogRecord(
            ['level' => 'ERROR', 'time' => $time]
        );
        $this->assertSame($time, $record->getTime());
    }

    public function testMicrosecond() {
        $time = microtime(true);
        $record = new LogRecord(
            ['level' => 'ERROR', 'time' => $time]
        );
        $this->assertSame(
            sprintf('%.6F', $time),
            $record->getTime()->format('U.u')
        );
    }

    /**
     * @expectedException Hyperframework\Logging\LoggingException
     */
    public function testInvalidTime() {
        new LogRecord(['level' => 'ERROR', 'time' => 'invalid string']);
    }

    /**
     * @expectedException Hyperframework\Logging\LoggingException
     */
    public function testLevelNotFound() {
        new LogRecord([]);
    }

    public function testMessage() {
        $time = new DateTime;
        $record = new LogRecord(
            ['level' => 'ERROR', 'message' => 'message']
        );
        $this->assertSame('message', $record->getMessage());
    }
}
