<?php
namespace Hyperframework\Routing;

class Router {
    public static function execute() {
        $result = DashParameterFilter::execute();
        $result2 = HierarchyFilter::execute($result['path']);
        if (count($result['parameters']) > 0) {
            static::verify($result['path'], $result['parameters']);
        }
        if ($result2 === null) {
            return $result['path'];
        } else {
           $tmp = explode($_SERVER['REQUEST_URI'], '?', 2);
           if ($result2 === HierarchyFilter::REDIRECT_TO_FILE) {
               $path = substr($tmp[0], 0, strlen($tmp[0]) - 1);
           } else {
               $path = $tmp[0] . '/';
           }
           static::redirect(
               static::getProtocol($path) .
               '://' .
               static::getDomain($path) .
               $path
           );
        }
    }

    protected static function verify($path, $parameters) {
        $pathInfo = PathInfo::get($path);
        if (isset($pathInfo['Link']['verification'])) {
            $pathInfo['Link']['class']::verify($parameters);
        }
    }

    protected static function getProtocol($path) {
        if (isset($_SERVER['HTTPS'])) {
            return 'https';
        }
        return 'http';
    }

    protected static function getDomain($path) {
        return $_SERVER['HOST_NAME'];
    }

    protected static function redirect($location) {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET' ||
            $_SERVER['REQUEST_METHOD'] !== 'HEAD') {
            return;
        }
        header('HTTP/1.1 301 Moved Permanently');
        header('Location: ' . $location);
    }
}
