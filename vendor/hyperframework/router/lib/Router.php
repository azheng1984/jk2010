<?php
namespace Hyperframework;

class Router {
    private static $parameters = array();

    public static function execute() {
        if ($_SERVER['SERVER_NAME'] !== $_SERVER['HTTP_HOST']) {
            static::redirect($_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI']);
            return;
        }
        $segments = explode('?', $_SERVER['REQUEST_URI'], 2);
        if ($segments[0] === '/') {
            return '/';
        }
        $path = '';
        foreach (explode('/', $segments[0]) as $segment) {
            if ($segment === '') {
                if ($path !== '') {
                    $path .= '/';
                }
                continue;
            }
            $splittingPosition = strpos($segment, '-');
            if ($splittingPosition === false) {
                $path .= '/' . $segment;
                static::$parameters[] = null;
                continue;
            }
            $items = explode('-', $segment, 2);
            $path .= '/' . $items[0];
            static::$parameters[] = $items[1];
        }
        return static::checkPath();
    }

    protected static function checkPath() {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            return $path;
        }
        if (Web\PathInfo::exists($path)) {
            return $path;
        }
        if (substr($path, -1) === '/') {
            $path = substr($path, 0, strlen($path) - 1);
            if (Web\PathInfo::exists($path)) {
                $location = substr($segments[0], 0, strlen($segments[0]) - 1);
                if (count($segments) === 2) {
                    $location .= '?' . $segments[1];
                }
                static::redirect($location);
                return;
            } else {
                throw new Web\NotFoundException;
            }
        }
        $path = $path . '/';
        if (Web\PathInfo::exists($path)) {
            $location = $segments[0] . '/';
            if (count($segments) === 2) {
                $location .= '?' . $segments[1];
            }
            static::redirect($location);
            return;
        }
        throw new Web\NotFoundException;
    }

    public static function getParameters() {
        return static::$parameters;
    }

    private static function redirect($location) {
        $protocol = 'http';
        if (isset($_SERVER['HTTPS'])) {
            $protocol = 'https';
        }
        $location = 'Location: ' . $protocol .
            '://' . $_SERVER['SERVER_NAME'] . $location;
        header($location);
        header('HTTP/1.1 301 Moved Permanently');
    }
}
