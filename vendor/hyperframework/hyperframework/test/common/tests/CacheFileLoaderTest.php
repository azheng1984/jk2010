<?php
namespace Hyperframework\Common;

use Hyperframework\Common\Test\TestCase as Base;

class CacheFileLoaderTest extends Base {
    public function testDefaultRootPath() {
        $path = $this->callProtectedMethod(
            'Hyperframework\Common\CacheFileLoader', 'getDefaultRootPath'
        );
        $this->assertSame(
            dirname(__DIR__) . DIRECTORY_SEPARATOR
                . 'tmp' . DIRECTORY_SEPARATOR . 'cache',
            $path
        );
    }
}

