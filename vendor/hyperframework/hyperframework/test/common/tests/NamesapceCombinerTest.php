<?php
namespace Hyperframework\Common;

use Hyperframework\Common\Test\TestCase as Base;

class NamesapceCombinerTest extends Base {
    public function testAppend() {
        $name = 'Namespace';
        NamespaceCombiner::append($name, 'Class');
        $this->assertSame('Namespace\Class', $name);
    }

    public function testAppendRootNamespace() {
        $name= '\\';
        NamespaceCombiner::append($name, 'Class');
        $this->assertSame('\Class', $name);
    }

    public function testAppendNamespaceWhichEndsWithNamespaceSeparator() {
        $name = 'Namespace\\';
        NamespaceCombiner::append($name, 'Class');
        $this->assertSame('Namespace\Class', $name);
    }

    public function testAppendEmpty() {
        $name = 'Class';
        NamespaceCombiner::append($name, null);
        $this->assertSame('Class', $name);
    }

    public function testPrepend() {
        $name = 'Class';
        NamespaceCombiner::prepend($name, 'Namespace');
        $this->assertSame('Namespace\Class', $name);
    }
}
