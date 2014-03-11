<?php
namespace Hyperframework\Web;

class ViewDispatcher {
    public static function run($mediaType, $pathInfo) {
        $isMediaTypeExists =
            in_array($_SERVER['REQUEST_MEDIA_TYPE'], $pathInfo['views']);
        if ($isMediaTypeExists === false) {
            throw new UnsupportedMediaTypeException;
        }
        $class = $pathInfo['namespace'] . '\\' . $mediaType;
        $view = new $class;
        $view->render();
    }
}
