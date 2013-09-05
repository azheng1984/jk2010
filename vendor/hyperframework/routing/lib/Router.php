<?php
namespace Hyperframework\Routing;

class Router {
    public static function execute() {
        $result = static::parse();
        $redirectType = HierarchyChecker::check($result['path']);
        if (static::hasPrarameter($result['parameters'])) {
            $path = $result['path'];
            if ($redirectType === HierarchyChecker::FILE_PATH) {
                $path = substr($path, 0, strlen($path) - 1);
            } elseif ($redirectType === HierarchyChecker::DIRECTORY_PATH) {
                $path = $path . '/';
            }
            static::initializeLink($path, $result['parameters']);
        }
        if ($redirectType !== null) {
            $path = null;
            $tmp = explode('?', $_SERVER['REQUEST_URI'], 2);
            if ($redirectType === HierarchyChecker::FILE_PATH) {
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

    protected static function parse() {
        return PathInExtensionParser::execute();
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
