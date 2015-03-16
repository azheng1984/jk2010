<?php
namespace Hyperframework\Common;

use Hyperframework\Common\Test\TestCase as Base;

class FilePathCombinerTest extends Base {
    public function testAppend() {
        $path = 'dir';
        FilePathCombiner::append($path, 'file');
        $this->assertSame('dir' . DIRECTORY_SEPARATOR . 'file', $path);
    }

    public function testAppendRootPath() {
        $path = DIRECTORY_SEPARATOR;
        FilePathCombiner::append($path, 'file');
        $this->assertSame(DIRECTORY_SEPARATOR . 'file', $path);
    }

    public function testAppendPathWhichEndsWithDirectorySeparator() {
        $path = 'dir' . DIRECTORY_SEPARATOR;
        FilePathCombiner::append($path, 'file');
        $this->assertSame('dir' . DIRECTORY_SEPARATOR . 'file', $path);
    }

    public function testAppendEmpty() {
        $path = 'file';
        FilePathCombiner::append($path, null);
        $this->assertSame('file', $path);
    }

    public function testPrepend() {
        $path = 'file';
        FilePathCombiner::prepend($path, 'dir');
        $this->assertSame('dir' . DIRECTORY_SEPARATOR . 'file', $path);
    }
}
