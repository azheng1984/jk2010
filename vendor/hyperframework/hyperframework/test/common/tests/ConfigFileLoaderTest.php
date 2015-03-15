<?php
namespace Hyperframework\Common;

use Hyperframework\Common\Test\TestCase as Base;

class ConfigFileLoaderTest extends Base {
    public function testDefaultRootPath() {
        $path = $this->callProtectedMethod(
            'Hyperframework\Common\ConfigFileLoader', 'getDefaultRootPath'
        );
        $this->assertSame(
            dirname(__DIR__) . DIRECTORY_SEPARATOR . 'config', $path
        );
    }
}

