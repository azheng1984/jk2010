<?php
namespace Hyperframework\Routing;

class Router {
    public static function execute() {
        $result = DashParameterFilter::execute();
        $result2 = HierarchyFilter::execute($result['path']);
        if (static::hasPrarameter($result['parameters'])) {
            $path = $result['path'];
            if ($result2 === HierarchyFilter::REDIRECT_TO_FILE) {
                $path = substr($path, 0, strlen($path) - 1);
            } elseif ($result2 === HierarchyFilter::REDIRECT_TO_DIRECTORY) {
                $path = $path . '/';
            }
            static::initializeLink($path, $result['parameters']);
        }
        if ($result2 !== null) {
            $path = null;
            $tmp = explode('?', $_SERVER['REQUEST_URI'], 2);
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
            return;
        }
        return $result['path'];
    }

    private static function hasPrarameter($parameters) {
       foreach ($parameters as $item) {
            if ($item !== null) {
                return true;
            }
        }
        return false;
    }

    protected static function initializeLink($path, $parameters) {
        $pathInfo = \Hyperframework\Web\PathInfo::get($path);
        if (isset($pathInfo['Link']['initialization'])) {
            $pathInfo['Link']['class']::initialization($parameters);
        }
    }

    protected static function getProtocol($path) {
        if (isset($_SERVER['HTTPS'])) {
            return 'https';
        }
        return 'http';
    }

    protected static function getDomain($path) {
        return $_SERVER['HTTP_HOST'];
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
