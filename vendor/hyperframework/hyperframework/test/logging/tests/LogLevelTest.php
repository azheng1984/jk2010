<?php
namespace Hyperframework\Logging;

use Hyperframework\Test\TestCase as Base;
use Hyperframework\Common\Config;

class LogLevelTest extends Base {
    public function testGetName() {
        $this->assertSame('FATAL', LogLevel::getName(0));
    }

    public function testGetNameByInvalidCode() {
        $this->assertSame(null, LogLevel::getName(-1));
    }

    public function testGetCode() {
        $this->assertSame(0, LogLevel::getCode('fatal'));
    }

    public function testGetCodeByInvalidName() {
        $this->assertSame(null, LogLevel::getCode('unknown'));
    }
}
