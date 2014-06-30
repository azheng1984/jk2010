<?php
namespace Hyperframework\Web;

class Router {
    final public static function run($ctx, $segments = null) {
        if ($segments === null) {
            $segments = RequestPath::getSegments();
        }
        $params = array();
        $path = '';
        foreach ($segments as $segment) {
            if ($segment === 'item') {
                throw new NotFoundException;
            }
            if (static::isParam($segment)) {
                $path .= 'item';
                $params[] = $segment;
            }
            $path .= '/' . $segment;
        }
        if ($path === '') {
            return '/';
        }
        $paramCount = count($params);
        if ($paramCount > 0) {
            $ctx->setParam('ids', $params);
            $ctx->setParam('id', $params[$paramCount - 1]);
        }
        if (strrpos(end($segments), '.') === false) {
            return $path;
        }
        $extensionPosition = strrpos($path, '.');
        $_SERVER['REQUEST_MEDIA_TYPE'] = substr(
            $path, $extensionPosition + 1
        );
        return substr($path, 0, $extensionPosition);
    }

    protected static function isParam($segment) {
        return ctype_digit($segment);
    }
}
