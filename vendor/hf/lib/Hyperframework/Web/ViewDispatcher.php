<?php
namespace Hyperframework\Web;

class ViewDispatcher {
    public function run($pathInfo) {
        $class = $pathInfo['namespace'] . $this->getMediaType($pathInfo);
        $view = new $class;
        $view->render();
    }

    private function getMediaType($pathInfo) {
        $views = $pathInfo['views'];
        if (is_string($views)) {
            $views = array($views);
        }
        if (isset($_SERVER['REQUEST_MEDIA_TYPE']) === false) {
            return $views[0];
        }
        if (in_array($_SERVER['REQUEST_MEDIA_TYPE'], $views) === false) {
            throw new Exceptions\UnsupportedMediaTypeException;
        }
        return $_SERVER['REQUEST_MEDIA_TYPE'];
    }
}
