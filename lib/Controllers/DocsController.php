<?php
namespace Hyperframework\Blog\Controllers;

use Hyperframework\Web\Controller;
use Hyperframework\Common\FileFullPathBuilder;
use Michelf\MarkdownExtra;
use Hyperframework\Web\NotFoundException;

class DocsController extends Controller {
    public function doShowAction() {
        $name = $this->getRouteParam('name');
        //todo check name pattern(security)
        if ($name !== null) {
            $path = FileFullPathBuilder::build(
                "vendor/hyperframework/hyperframework/docs/$name.md"
            );
            if (file_exists($path)) {
                $html = MarkdownExtra::defaultTransform(file_get_contents($path));
            } else {
                throw new NotFoundException;
            }
        }
        $result = ['title' => '文档 - Hyperframework'];
        if ($name !== null) {
            $result['doc'] = $html;
        }
        $result['top_nav'] = 'docs';
        return $result;
    }
}
