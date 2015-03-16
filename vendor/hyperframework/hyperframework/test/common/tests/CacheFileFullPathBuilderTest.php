<?php
namespace Hyperframework\Common;

use Hyperframework\Common\Test\TestCase as Base;

class CacheFileFullPathBuilderTest extends Base {
    public function testGetRootPath() {
        $path = $this->callProtectedMethod(
            'Hyperframework\Common\CacheFileFullPathBuilder', 'getRootPath'
        );
        $this->assertSame(
            dirname(__DIR__) . DIRECTORY_SEPARATOR
                . 'tmp' . DIRECTORY_SEPARATOR . 'cache',
            $path
        );
    }
}
