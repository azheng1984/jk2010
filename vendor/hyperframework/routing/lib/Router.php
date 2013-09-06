<?php
namespace Hyperframework\Routing;

class Router {
    public static function execute($path = null) {
        $result = static::parse($path);
        $redirectType = HierarchyChecker::check($result['path']);
        if (static::hasPrarameter($result['parameters'])) {
            $path = $result['path'];
            if ($redirectType === HierarchyChecker::FILE) {
                $path = substr($path, 0, strlen($path) - 1);
            } elseif ($redirectType === HierarchyChecker::DIRECTORY) {
                $path = $path . '/';
            }
            static::initializeLink($path, $result['parameters']);
        }
        if ($redirectType !== null) {
            $path = null;
            $tmp = explode('?', $_SERVER['REQUEST_URI'], 2);
            if ($redirectType === HierarchyChecker::FILE) {
                $path = substr($tmp[0], 0, strlen($tmp[0]) - 1);
            } else {
                $path = $tmp[0] . '/';
            }
            $queryString = '';
            if (isset($tmp[1])) {
                $queryString = '?' . $tmp[1];
            }
            static::redirect(static::getLocation($path, $queryString));
            return;
        }
        return $result['path'];
    }

    protected static function parse($path = null) {
        return PathInExtensionParser::parse($path);
    }


    protected static function initializeLink($path, $parameters) {
        $pathInfo = \Hyperframework\Web\PathInfo::get($path);
        if (isset($pathInfo['Link']['initialization'])) {
            $pathInfo['Link']['class']::initialization($parameters);
        }
    }

    protected static function getLocation($path, $queryString){
        return static::getProtocol($path) . '://' .
            static::getDomain($path) . $path . $queryString;
    }

    protected static function getProtocol() {
        if (isset($_SERVER['HTTPS'])) {
            return 'https';
        }
        return 'http';
    }

    protected static function getDomain() {
        return $_SERVER['HTTP_HOST'];
    }

    protected static function redirect($location) {
        if ($_SERVER['REQUEST_METHOD'] === 'GET' || 
            $_SERVER['REQUEST_METHOD'] === 'HEAD') {
            header('HTTP/1.1 301 Moved Permanently');
            header('Location: ' . $location);
        }
    }

    private static function hasPrarameter($parameters) {
       foreach ($parameters as $item) {
            if ($item !== null) {
                return true;
            }
        }
        return false;
    }
}
