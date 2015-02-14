<?php
namespace Hyperframework\Logging;

use Hyperframework\Test\TestCase as Base;

class LogLevelTest extends Base {
    public function testGetName() {
        $this->assertSame('ERROR', LogLevel::getName(1));
    }

    public function testGetNameByInvalidCode() {
        $this->assertSame(null, LogLevel::getName(-1));
    }

    public function testGetCode() {
        $this->assertSame(1, LogLevel::getCode('error'));
    }

    public function testGetCodeByInvalidName() {
        $this->assertSame(null, LogLevel::getCode('unknown'));
    }
}
