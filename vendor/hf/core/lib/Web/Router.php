<?php
namespace Hyperframework;

class Router {
    public static function getPath($path) {
        if (substr($path, -1) === '/') {
            return substr($path, 0, -1);
        }
        $dotPosition = strpos($path, '.');
        if ($dotPosition !== false) {
            $_SERVER['REQUEST_MEDIA_TYPE'] = substr($dotPosition, $dotPosition);
            return substr($path, 0, $dotPosition);
        }
    }
}
