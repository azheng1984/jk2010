<?php
namespace Hyperframework\Web;

class ViewDispatcher {
    public static function run($pathInfo, $mediaType) {
        if (in_array($mediaType, $pathInfo['views']) === false) {
            throw new UnsupportedMediaTypeException;
        }
        $class = $pathInfo['namespace'] . '\\' . $mediaType;
        $view = new $class;
        $view->render();
    }
}
