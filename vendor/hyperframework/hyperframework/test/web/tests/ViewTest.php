<?php
namespace Hyperframework\Web;

use Hyperframework\Common\Config;
use Hyperframework\Test\TestCase as Base;

class ViewTest extends Base {
    public function testConstruct() {
        $this->expectOutputString('view: index/index');
        Config::set('hyperframework.app_root_path', dirname(__DIR__));
        $path = null;
        $view = new View;
        $view->render('index/index.html.php');
    }
}
