<?php
namespace Hyperframework\Web;

use Hyperframework\Common\Config;
use Hyperframework\Web\Test\ViewTemplate;
use Hyperframework\Web\Test\TestCase as Base;

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

    public function testRenderByFullPath() {
    }

    public function testRenderLayout() {
    }

    public function testRenderNestedLayout() {
    }

    public function testRenderNestedView() {
    }

    public function testNestedViewLayout() {
    }

    /**
     * @expectedException Hyperframework\Web\ViewException
     */
    public function testRenderByEmptyPath() {
        $tpl = new ViewTemplate(function() {});
        $tpl->render(null);
    }

    public function testRenderBlock() {
        $tpl = new ViewTemplate(function() {});
        $isRendered = false;
        $tpl->setBlock('name', function() use (&$isRendered) {
            $isRendered = true;
        });
        $tpl->renderBlock('name');
        $this->assertTrue($isRendered);
    }

    public function testRenderDefaultBlock() {
        $tpl = new ViewTemplate(function() {});
        $isRendered = false;
        $tpl->renderBlock('undefined', function() use (&$isRendered) {
            $isRendered = true;
        });
        $this->assertTrue($isRendered);
    }

    public function testRenderBlockLayout() {
    }

    /**
     * @expectedException Hyperframework\Web\ViewException
     */
    public function testRenderBlockWhichDoesNotExist() {
        $tpl = new ViewTemplate(function() {});
        $tpl->renderBlock('undefined');
    }
}
