<?php
namespace Hyperframework\Web;

use Hyperframework;
use Hyperframework\Config;
use Hyperframework\FullPathRecognizer;

class ViewTemplate extends ViewTemplateEngine {
    public function render($name) {
        $path = Config::get('hyperframework.web.view.root_path');
        if ($path === null) {
            $path = 'views';
        }
        if (FullPathRecognizer::isFull($path) === false) {
            $path = Hyperframework\APP_ROOT_PATH . DIRECTORY_SEPARATOR . $path;
        }
        $path .= DIRECTORY_SEPARATOR . $name;
        require $path;
    }
}
