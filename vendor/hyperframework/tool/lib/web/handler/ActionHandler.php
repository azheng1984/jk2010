<?php
class ActionHandler {
    public function handle($class, $fullPath) {
        $postfix = 'Action';
        if (substr($class, -(strlen($postfix))) !== $postfix) {
            return;
        }
        return $this->getCache($class, $fullPath);
    }

    private function getCache($class, $fullPath) {
        $cache = array('class' => $class, 'methods' => array());
        $httpMethods = array('GET', 'POST', 'PUT', 'DELETE');
        if ($this->hasPrivateGet($class)) {
            $cache['GET_not_allowed'] = true;
        }
        $reflectors = $this->getMethodReflectors($class, $fullPath);
        foreach ($reflectors as $reflector) {
            $method = $reflector->getName();
            if (strpos($method, '__') === 0) {
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
                    "Error: Invalid public method '$method' in '$fullPath'"
                );
            }
            $cache['methods'][$method] = true;
        }
        if (count($cache['methods']) === 0) {
            unset($cache['methods']);
        }
        if (count($cache) === 1) {
            echo "Warning: Empty action in '$fullPath'" . PHP_EOL;
        }
        return $cache;
    }

    private function getMethodReflectors($class, $fullPath) {
        $reflector = new ReflectionClass($class);
        return $reflector->getMethods(ReflectionMethod::IS_PUBLIC);
    }

    private function hasPrivateGet($class) {
        $reflector = new ReflectionClass($class);
        if ($reflector->hasMethod('GET') === false) {
            return false;
        }
        $getMethod = $reflector->getMethod('GET');
        return $getMethod->isPrivate();
    }
}
