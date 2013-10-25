<?php
namespace Hyperframework\Web;

class ViewProcessor {
    public function run($info) {
        if (isset($_SERVER['REQUEST_MEDIA_TYPE']) === false) {
            $_SERVER['REQUEST_MEDIA_TYPE'] = key($info);
        }
        $mediaType = $_SERVER['REQUEST_MEDIA_TYPE'];
        if (isset($info[$mediaType]) === false) {
            throw new UnsupportedMediaTypeException;
        }
        $class = $info['namespace'] . $info[$mediaType];
        $view = new $class;
        $view->render();
    }
}
