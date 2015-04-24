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
                    case 'web': $nav = 'Web 应用开发框架'; break;
                    case 'cli': $nav = '命令行应用开发框架'; break;
                    case 'common': $nav = 'Common 模块'; break;
                    case 'db': $nav = 'Db 模块'; break;
                    case 'logging': $nav = 'Logging 模块'; break;
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
        $result = ['title' => '文档 - hyperframework'];
        $result['doc'] = $html;
        $result['nav'] = $nav;
        return $result;
    }
}
