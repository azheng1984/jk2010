<?php
namespace Hyperframework\Web;

use Hyperframework\Common\Config;
use Hyperframework\Web\Test\TestCase as Base;

class ViewFactoryTest extends Base {
    public function testCreateView() {
        $this->assertInstanceOf(
            'Hyperframework\Web\View', ViewFactory::createView(null)
        );
    }
}
