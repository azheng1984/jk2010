<?php
namespace Hyperframework\Web;

class ViewDispatcher {
    public function run($pathInfo) {
        $view = $this->getView($pathInfo);
        $view->render();
    }

    protected function getView($pathInfo) {
        $class = $pathInfo['namespace'] . '\\' . $this->getMediaType($pathInfo);
        return new $class;
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
