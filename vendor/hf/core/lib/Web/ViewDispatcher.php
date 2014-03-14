<?php
namespace Hyperframework\Web;

class ViewDispatcher {
    public static function run($mediaType, $pathInfo) {
        static::checkView($mediaType, $pathInfo);
        static::dispatch($mediaType, $pathInfo);
    }

    protected static function checkView($mediaType, $pathInfo) {
        if (isset($pathInfo['views']) === false
            || in_array($mediaType, $pathInfo['views']) === false) {
            throw new UnsupportedMediaTypeException;
        }
    }

    protected static function dispatch($mediaType, $pathInfo) {
        $class = $pathInfo['namespace'] . '\\' . $mediaType;
        $view = new $class;
        $view->render();
    }
}
