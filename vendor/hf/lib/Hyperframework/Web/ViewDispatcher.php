<?php
namespace Hyperframework\Web;

class ViewDispatcher {
    public function run($pathInfo) {
        $class = $this->getViewClass($pathInfo);
        if ($class !== null) {
            $view = new $class;
            $view->render();
        }
    }

    private function getViewClass($pathInfo) {
        if (isset($pathInfo['views'])) {
            //todo return default app views by Config or return null
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
