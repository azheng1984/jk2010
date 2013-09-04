<?php
namespace Hyperframework\Router;
use Hyperframework\Web\PathInfo as PathInfo;
use Hyperframework\Web\NotFoundException as NotFoundException;

class HierarchyFilter {
    public static function execute($processedUri = null, $requestUri = null) {
        if ($requestUri === null) {
            $requestUri = $_SERVER['REQUEST_URI'];
        }
        $orignalSegments = explode('?', $requestUri, 2);
        if ($processedUri === null) {
            $segments = $orignalSegments;
        } else {
            $segments = explode('?', $processedUri, 2);
        }
        //move to checkuri
        if ($segments[0] === '/') {
            return static::check($segments, '/');
        }
        $path = $segment[0];
        if (PathInfo::exists($path)) {
            return static::check($orignalSegments, $path);
        }
        if (substr($path, -1) === '/') {
            $path = substr($path, 0, strlen($path) - 1);
            if (PathInfo::exists($path)) {
                $orignalSegments[0] = substr($orignalSegments[0], 0, strlen($orignalSegments[0]) - 1);
                return static::check($orignalSegments, $path);
            } else {
                throw new NotFoundException;
            }
        }
        $path = $path . '/';
        if (PathInfo::exists($path)) {
            $orignalSegments[0] = $orignalSegments[0] . '/';
            return static::check($orignalSegments, $path . '/');
        }
        throw new NotFoundException;
    }

    private static function check($segments, $path) {
        $location = static::getLocation($segments);
        if (LocationMatcher::execute($location) === false) {
            return false;
        }
        return $path;
    }

    protected static function getLocation($segments) {
        $location = static::getProtocol() . '://' .
           static::getDomain() . $segments[0];
        if (isset($segments[1])) {
            $location .= '?' . $segments[1];
        }
        return $location;
    }

    protected static function getProtocol() {
        if (isset($_SERVER['HTTPS'])) {
            return 'https';
        }
        return  'http';
    }

    protected static function getDomain() {
        return $_SERVER['SERVER_NAME'];
    }
}
