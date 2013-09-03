<?php
namespace Hyperframework\Router;

//router chain 的目的时把 url 变成 app path, 并对跨越 action 的参数进行处理
class HierarchyFilter {
    /**
     * @return transformed uri => path
     */
    public static function execute($uri = null) {
        if ($uri === null) {
            $orignalSegments = explode('?', $_SERVER['REQUEST_URI'], 2);
            $segments = $orignalSegments;
        } else {
            $orignalSegments = explode('?', $_SERVER['REQUEST_URI'], 2);
            $segments = explode('?', $uri, 2);
        }
        if ($segments[0] === '/') {
            return static::check($segments, '/');
        }
        $path = $segment[0];
        if (Web\PathInfo::exists($path)) {
            return static::check($orignalSegments, $path);
        }
        if (substr($path, -1) === '/') {
            $path = substr($path, 0, strlen($path) - 1);
            if (Web\PathInfo::exists($path)) {
                $orignalSegments[0] = substr($orignalSegments[0], 0, strlen($orignalSegments[0]) - 1);
                return static::check($orignalSegments, $path);
            } else {
                throw new Web\NotFoundException;
            }
        }
        $path = $path . '/';
        if (Web\PathInfo::exists($path)) {
            $orignalSegments[0] = $orignalSegments[0] . '/';
            return static::check($orignalSegments, $path . '/');
        }
        throw new Web\NotFoundException;
    }

    private static function check($segments, $path) {
        $location = 'http://' . $_SERVER['SERVER_NAME'] . $segments[0];
        if (isset($segments[1])) {
            $location .= '?' . $segments[1];
        }
        if (LocationMatcher::execute($location) === false) {
            return false;
        }
        return $path;
    }
}
