<?php
namespace Hyperframework\Web;

class ViewDispatcher {
    public static final function run($pathInfo) {
        $class = $pathInfo['namespace'] . '\\'
            . static::getMediaType($pathInfo['views']);
        $view = new $class;
        $view->render();
    }

    private static function getMediaType($views) {
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
