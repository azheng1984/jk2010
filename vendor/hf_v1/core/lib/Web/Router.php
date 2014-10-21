<?php
namespace Hyperframework\Web;

class Router {
    final public static function execute($ctx, $segments = null) {
        if ($segments === null) {
            $segments = RequestPath::getSegments();
        }
        $params = array();
        $path = '';
        foreach ($segments as $segment) {
            if ($segment === 'item') {
                throw new NotFoundException;
            }
            if (static::isId($segment)) {
                $path .= '/item';
                $params[] = $segment;
                continue;
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
        if (strrpos(end($segments), '.') < 1) {
            return $path;
        }
        $extensionPosition = strrpos($path, '.');
        $_SERVER['REQUEST_MEDIA_TYPE'] = substr($path, $extensionPosition + 1);
        return substr($path, 0, $extensionPosition);
    }

    protected static function isId($segment) {
        return ctype_digit($segment);
    }
}
