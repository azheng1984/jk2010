<?php
namespace Hyperframework\Web;

use Hyperframework\Config;
use Hyperframework\FileLoader;

class ViewTemplate extends ViewTemplateEngine {
    public function render($name) {
        $path = Config::get('hyperframework.web.view.root_path');
        if ($path === null) {
            $path = 'views';
        }
        $path .= DIRECTORY_SEPARATOR . $name;
        FileLoader::loadPhp($path);
    }
}
