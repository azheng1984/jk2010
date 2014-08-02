<?php
namespace Hyperframework\Web;

class ActionInfoBuilder {
    public static function run($class, &$pathInfo) {
        $cache = array('class' => $class, 'methods' => array());
        if (self::hasPrivateGetMethod($class)) {
            $cache['get_not_allowed'] = true;
        }
        $reflectors = self::getMethodReflectors($class);
        foreach ($reflectors as $reflector) {
            $method = $reflector->getName();
            if (strncmp($method, '__', 2) === 0) {
                continue;
            }
            if ($method === 'before') {
                $cache['before_filter'] = true;
                continue;
            }
            if ($method === 'after') {
                $cache['after_filter'] = true;
                continue;
            }
            $cache['methods'][] = strtoupper($method);
        }
        if (count($cache['methods']) === 0) {
            unset($cache['methods']);
        }
        $pathInfo['action'] = $cache;
    }

    private static function getMethodReflectors($class) {
        $reflector = new \ReflectionClass($class);
        return $reflector->getMethods(\ReflectionMethod::IS_PUBLIC);
    }

    private static function hasPrivateGetMethod($class) {
        $reflector = new \ReflectionClass($class);
        if ($reflector->hasMethod('get') === false) {
            return false;
        }
        $getMethod = $reflector->getMethod('get');
        return $getMethod->isPrivate();
    }
}
