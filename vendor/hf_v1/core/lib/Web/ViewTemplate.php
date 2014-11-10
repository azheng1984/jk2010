<?php
namespace Hyperframework\Web;

use Closure;
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
        $root = $path;
        $path .= DIRECTORY_SEPARATOR . $name;
        $function = $this->renderTemplate($path);
        $layout = $this->getLayout();
        if ($this->getLayout() !== null) {
            do { 
                $layout = $this->getLayout();
                $this->setLayout(null);
                $this->renderTemplate($root . '/_layouts/' . $layout);
            } while ($this->getLayout() !== null);
        } else {
            if ($function instanceof Closure) {
                $function();
            }
        }
    }

    private function renderTemplate($path) {
        return include $path;
    }
}
