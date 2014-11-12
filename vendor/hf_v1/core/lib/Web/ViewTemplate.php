<?php
namespace Hyperframework\Web;

use Hyperframework\FullPathRecognizer;

class ViewTemplate extends ViewTemplateEngine {
    private $path;

    protected function renderTemplate($path) {
        if (FullPathRecognizer::isFull($path) === false) {
            $path = $this->getViewRootPath() . DIRECTORY_SEPARATOR . $path;
        }
        $this->path = $path;
        unset($path);
        include $this->path;
    }
}
