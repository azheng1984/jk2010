<?php
namespace Hyperframework\Web;

use Hyperframework\Common\Config;
use Hyperframework\Web\Test\TestCase as Base;

class ViewTest extends Base {
    public function testConstruct() {
        $this->expectOutputString('view: index/index');
        $path = null;
        $view = new View;
        $view->render('index/index.html.php');
    }
}
