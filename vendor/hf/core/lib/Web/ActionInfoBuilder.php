<?php
namespace Hyperframework\Web;

class ActionInfoBuilder {
    public static function run($class, &$pathInfo) {
        $className = $class;
        $cache = array('class' => $className, 'methods' => array());
        //todo: configurable
        $httpMethods = array('get', 'post', 'patch', 'put', 'delete');
        if ($this->hasPrivateGetMethod($class)) {
            $cache['get_not_allowed'] = true;
        }
        $reflectors = $this->getMethodReflectors($class);
        foreach ($reflectors as $reflector) {
            $method = strtoupper($reflector->getName());
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
            if (in_array($method, $httpMethods) === false) {
                throw new Exception(
                    "Error: Invalid public method '$method' in '$class'"
                );
            }
            $cache['methods'][$method] = true;
        }
        if (count($cache['methods']) === 0) {
            unset($cache['methods']);
        }
        $pathInfo['action'] = $cache;
    }

    private function getMethodReflectors($class) {
        $reflector = new ReflectionClass($class);
        return $reflector->getMethods(ReflectionMethod::IS_PUBLIC);
    }

    private function hasPrivateGetMethod($class) {
        $reflector = new ReflectionClass($class);
        if ($reflector->hasMethod('get') === false) {
            return false;
        }
        $getMethod = $reflector->getMethod('get');
        return $getMethod->isPrivate();
    }
}
