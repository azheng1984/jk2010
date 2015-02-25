<?php
namespace Hyperframework\Db;

use Hyperframework\Common\Config;
use Hyperframework\Db\Test\TestCase as Base;

class DbProfilerTest extends Base {
    public function testIsEnabled() {
        Config::set('hyperframework.db.profiler.enable', false);
        $this->assertFalse(DbProfiler::isEnabled());
        Config::set('hyperframework.db.profiler.enable', true);
        $this->assertTrue(DbProfiler::isEnabled());
    }
}
