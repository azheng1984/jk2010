<?php
class DashParameterFilter {
    private static $parameters = array();

    public static function execute($processedUri = null, $requestUri = null) {
        if ($requestUri === null) {
            $requestUri = $_SERVER['REQUEST_URI'];
        }
        $requestSegments = explode('?', $requestUri, 2);
        if ($processedUri === null) {
            $segments = $requestSegments;
        } else {
            $segments = explode('?', $processedUri, 2);
        }
        if ($segments[0] === '/') {
            return static::next($processedUri, $requestUri);
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
    }

    private static function next($processedUri, $requestUri) {
        HierarchyFilter::execute($processedUri, $requestUri);
    }

    public static function getParameters() {
        return static::$parameters;
    }
}
