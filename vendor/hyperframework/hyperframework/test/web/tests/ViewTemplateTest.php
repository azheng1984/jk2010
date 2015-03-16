<?php
namespace Hyperframework\Web;

use Hyperframework\Common\Config;
use Hyperframework\Common\FileFullPathBuilder;
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
        $viewPath = FileFullPathBuilder::build(
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
        $this->expectOutputString("begin content end\n");
        $view = new View;
        $view->render('index/view_with_layout.php');
    }

    public function testRenderNestedLayout() {
        $this->expectOutputString("begin begin-sub content end-sub end\n");
        $view = new View;
        $view->render('index/view_with_nested_layout.php');
    }

    public function testRenderNestedViewWithLayout() {
        $this->expectOutputString("begin-out begin content end\n end-out\n");
        $view = new View;
        $view->render('index/nested_view.php');
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

    public function testRenderViewWithLayoutInBlock() {
        $this->expectOutputString("begin content end\n");
        $tpl = new View;
        $tpl->setBlock('name', function() use ($tpl) {
            $tpl->render('index/view_with_layout.php');
        });
        $tpl->renderBlock('name');
    }

    public function testIssetViewModelField() {
        $tpl = new ViewTemplate(function() {}, ['name' => 'value']);
        $this->assertTrue(isset($tpl['name']));
        $this->assertFalse(isset($tpl['unknown']));
    }

    public function testGetViewModelField() {
        $tpl = new ViewTemplate(function() {}, ['name' => 'value']);
        $this->assertSame('value', $tpl['name']);
    }

    /**
     * @expectedException Hyperframework\Web\ViewException
     */
    public function testGetViewModelFieldWhichDoesNotExist() {
        $tpl = new ViewTemplate(function() {}, []);
        $tpl['unknown'];
    }

    public function testUnsetViewModelField() {
        $tpl = new ViewTemplate(function() {}, ['name' => 'value']);
        $this->assertTrue(isset($tpl['name']));
        unset($tpl['name']);
        $this->assertFalse(isset($tpl['name']));
    }

    /**
     * @expectedException Hyperframework\Web\ViewException
     */
    public function testRenderBlockWhichDoesNotExist() {
        $tpl = new ViewTemplate(function() {});
        $tpl->renderBlock('undefined');
    }
}
