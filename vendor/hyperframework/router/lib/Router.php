<?php
namespace Hyperframework;
use Hyperframework\Web\PathInfo as PathInfo;

class Router {
    private static $parameters = array();

    public static function execute() {
        if ($_SERVER['SERVER_NAME'] !== $_SERVER['HTTP_HOST']) {
            static::redirect($_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']);
            return;
        }
        $segments = explode('?', $_SERVER['REQUEST_URI'], 2);
        if ($segments[0] === '/') {
            return '/';
        }
        $path = '';
        foreach (explode('/', $segments[0]) as $segment) {
            if ($segment === '') {
                continue;
            }
            $splittingPosition = strpos($segment, '-');
            if ($splittingPosition === false) {
                $path .= '/' . $segment;
                $parameters[] = null;
                continue;
            }
            $items = explode('-', $segment, 2);
            $path .= '/' . $items[0];
            $parameters[] = $items[1];
        }
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            return $path;
        }
        if (PathInfo::exists($path)) {
            return $path;
        }
        if (substr($path, -1) === '/') {
            $path = substr($path, 0, strlen($path) - 1);
            if (PathInfo::exists($path)) {
                $location = substr($segments[0], 0, strlen($path) - 1);
                if (count($segments) === 2) {
                    $location .= '?' . $segements[1];
                }
                static::redirect($location);
            } else {
                throw new Web\NotFoundException;
            }
        }
        $path = $path . '/';
        if (PathInfo::exists($path)) {
            $location = $segments[0] . '/';
            if (count($segments) === 2) {
                $location .= '?' . $segements[1];
            }
            static::redirect($location);
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
        header('Location: ' . $protocol . '://' . $location);
        header('HTTP/1.1 301 Moved Permanently');
    }
}
