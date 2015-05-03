<?php
namespace Hyperframework\Blog\Controllers;

use Hyperframework\Web\Controller;
use Hyperframework\Common\FileFullPathBuilder;
use Michelf\MarkdownExtra;
use Hyperframework\Web\NotFoundException;

class ManualController extends Controller {
    public function doShowAction() {
        $name = $this->getRouteParam('name');
        //todo check name pattern
        $nav = null;
        if ($name === null) {
            $name = 'index';
        } else {
            $nav = 'index';
            $tmp = explode('/', $name);
            if (count($tmp) > 1) {
                switch ($tmp[0]) {
                    case 'web': $nav = ['web', 'Web 应用开发框架']; break;
                    case 'cli': $nav = ['cli', '命令行应用开发框架']; break;
                    case 'common': $nav = ['common', 'Common 模块']; break;
                    case 'db': $nav = ['db', 'Db 模块']; break;
                    case 'logging': $nav = ['logging', 'Logging 模块']; break;
                }
            }
        }
        $path = FileFullPathBuilder::build(
            "vendor/hyperframework/hyperframework/manual/$name.md"
        );
        if (file_exists($path)) {
            $html = MarkdownExtra::defaultTransform(file_get_contents($path));
        } else {
            throw new NotFoundException;
        }
        $start = strpos($html, '<h1>') + 4;
        $end = strpos($html, '</h1>');
        $x = substr($html, $start, $end - $start);
        if ($nav !== null) {
            if ($nav === 'index') {
                $result = ['title' => $x . ' - Hyperframework 手册'];
            } else {
                $result = ['title' => $x . ' - ' .$nav[1] . ' - Hyperframework 手册'];
            }
        } else {
            $result = ['title' => 'Hyperframework 手册'];
        }
        $result['doc'] = $html;
        $result['nav'] = $nav;
        $result['top_nav'] = 'docs';
        return $result;
    }
}
