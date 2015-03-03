<?php
namespace Hyperframework\Web;

use Hyperframework\Common\Config;
use Hyperframework\Web\Test\TestCase as Base;

class ErrorViewTest extends Base {
    public function testRender() {
        $this->expectOutputString('404 not found');
        $engine = $this->getMock('Hyperframework\Web\ResponseHeaderHelperEngine');
        $engine->expects($this->once())->method('setHeader')->with(
            'content-type: text/plain; charset=utf-8'
        );
        ResponseHeaderHelper::setEngine($engine);
        $view = new ErrorView;
        $view->render(404, 'not found', null);
    }
}
