<?php
namespace Hyperframework\Logging;

use Hyperframework\Test\TestCase as Base;
use Hyperframework\Common\Config;

class LogLevelHelperTest extends Base {
    public function testGetNameByCode() {
        $this->assertSame('FATAL', LogLevelHelper::getName(0));
    }

    public function testGetNameByInvalidCode() {
        $this->assertSame(null, LogLevelHelper::getName(-1));
    }

    public function testGetCodeByName() {
        $this->assertSame(0, LogLevelHelper::getCode('fatal'));
    }

    public function testGetCodeByInvalidName() {
        $this->assertSame(null, LogLevelHelper::getCode('unknown'));
    }
}
