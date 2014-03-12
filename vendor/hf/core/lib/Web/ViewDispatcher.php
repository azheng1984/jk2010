<?php
namespace Hyperframework\Web;

class ViewDispatcher {
    public static function run($pathInfo, $mediaType) {
        $hasView = in_array($_SERVER['REQUEST_MEDIA_TYPE'], $pathInfo['views']);
        if ($hasView === false) {
            throw new UnsupportedMediaTypeException;
        }
        $class = $pathInfo['namespace'] . '\\' . $mediaType;
        $view = new $class;
        $view->render();
    }
}
