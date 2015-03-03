<?php
namespace Hyperframework\Web;

use Hyperframework\Common\Config;
use Hyperframework\Web\Test\TestCase as Base;

class ViewPathBuilderTest extends Base {
    public function testBuild() {
        $this->assertSame('index.html.php', ViewPathBuilder::build('index'));
    }
}
