<?php
namespace Hyperframework\Web;

use Hyperframework\Common\Config;
use Hyperframework\Test\TestCase as Base;
use Hyperframework\Web\Test\ViewTemplate;

class ViewTemplateTest extends Base {
    public function testRender() {
        Config::set('hyperframework.app_root_path', dirname(__DIR__));
        $path = null;
        $tpl = new ViewTemplate(function($arg) use (&$path) {$path = $arg;});
        $tpl->render('index/index.php');
        $this->assertSame(dirname(__DIR__)
            . DIRECTORY_SEPARATOR . 'views'  . DIRECTORY_SEPARATOR
            . 'index' . DIRECTORY_SEPARATOR . 'index.php',
        $path);
    }
}
