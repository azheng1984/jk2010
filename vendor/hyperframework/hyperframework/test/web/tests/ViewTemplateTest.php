<?php
namespace Hyperframework\Web;

use Hyperframework\Common\Config;
use Hyperframework\Common\FileLoader;
use Hyperframework\Web\Test\ViewTemplate;
use Hyperframework\Web\Test\TestCase as Base;

class ViewTemplateTest extends Base {
    public function testRender() {
        $path = null;
        $tpl = null;
        $tpl = new ViewTemplate(
            function() use (&$path, &$tpl) {$path = $tpl->getFilePath();}
        );
        $tpl->render('index/index.php');
        $this->assertSame(dirname(__DIR__)
            . DIRECTORY_SEPARATOR . 'views'  . DIRECTORY_SEPARATOR
            . 'index' . DIRECTORY_SEPARATOR . 'index.php',
        $path);
    }

    public function testRenderByFullPath() {
        $viewPath = FileLoader::getFullPath(
            'views' . DIRECTORY_SEPARATOR . 'index'
                . DIRECTORY_SEPARATOR . 'index.php'
        );
        $loadedPath = null;
        $tpl = null;
        $tpl = new ViewTemplate(
            function() use (&$loadedPath, &$tpl) {
                $loadedPath = $tpl->getFilePath();
            }
        );
        $tpl->render($viewPath);
        $this->assertSame($viewPath, $loadedPath);
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

    public function testIssetViewModelField() {
    }

    public function testGetViewModelField() {
    }

    public function testGetViewModelFieldWhichDoesNotExist() {
    }

    public function testUnsetViewModelField() {
    }

    /**
     * @expectedException Hyperframework\Web\ViewException
     */
    public function testRenderBlockWhichDoesNotExist() {
        $tpl = new ViewTemplate(function() {});
        $tpl->renderBlock('undefined');
    }
}
