<?php
namespace Hyperframework;

class Router {
    public static function run($urlPath) {
        if (substr($path, -1) === '/') {
            return substr($path, 0, -1);
        }
        $extensionPosition = strpos($path, '.');
        if ($extensionPosition === false) {
            return $urlPath;
        }
        $_SERVER['REQUEST_MEDIA_TYPE'] = substr($path, $extensionPosition + 1);
        return substr($path, 0, $extensionPosition);
    }
}
