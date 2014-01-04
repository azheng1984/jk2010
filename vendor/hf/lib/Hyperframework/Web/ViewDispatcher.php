<?php
namespace Hyperframework\Web;

class ViewDispatcher {
    public function run($pathInfo, $actionResult) {
        $class = $this->getViewClass($pathInfo);
        $view = new $class;
        $view->render($actionResult);
    }

    private function getViewClass($pathInfo) {
        if (isset($pathInfo['views'])) {
            //todo return default view for json/xml by Config
        }
        $views = $pathInfo['views'];
        if (is_string($views)) {
            $views = array($views);
        }
        if (isset($_SERVER['REQUEST_MEDIA_TYPE']) === false) {
            return $pathInfo['namespace'] . $views[0];
        }
        if (in_array($_SERVER['REQUEST_MEDIA_TYPE'], $views) === false) {
            throw new Exceptions\UnsupportedMediaTypeException;
        }
        return $pathInfo['namespace'] . $_SERVER['REQUEST_MEDIA_TYPE'];
    }
}
