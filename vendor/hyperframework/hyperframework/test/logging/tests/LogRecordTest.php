<?php
namespace Hyperframework\Logging;

use DateTime;
use Hyperframework\Test\TestCase as Base;
use Hyperframework\Common\Config;

class LogRecordTest extends Base {
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
//
//    public function testDateTimeForTimeOption() {
//        $time = new DateTime;
//        Logger::warn(['name' => 'test', 'message' => 'message', 'time' => $time]);
//        $this->assertSame(
//            $time->format('Y-m-d H:i:s') . ' [WARNING] message' . PHP_EOL,
//            file_get_contents(Config::getAppRootPath() . '/log/app.log')
//        );
//    }

//    public function testMessage() {
//        $handler = new LogHandler;
//        $time = time();
//        $handler->handle(new LogRecord([
//            'level' => 'ERROR',
//            'message' => 'message',
//            'time' => $time, 'name' => 'name'
//        ]));
//        $this->assertSame(
//            date("Y-m-d H:i:s", $time) . ' [ERROR] message' . PHP_EOL,
//            $this->getLogContent()
//        );
//    }

//    public function testName() {
//        $handler = new LogHandler;
//        $time = time();
//        $handler->handle(new LogRecord([
//            'level' => 'ERROR', 'name' => 'name', 'time' => $time
//        ]));
//        $this->assertSame(
//            date("Y-m-d H:i:s", $time) . ' [ERROR]' . PHP_EOL,
//            $this->getLogContent()
//        );
//    }
}
