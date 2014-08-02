<?php
namespace Hyperframework\Web;

class ViewInfoBuilder {
    public function run($namespace, $types, $order = null) {
        if ($order === null) {
            $order = array('Html', 'Xml', 'Json');
        } elseif (is_string($order)) {
            $order = array($order);
        }
        if (is_string($types)) {
            $types = array($types);
        }
        $views = array();
        foreach ($types as $type) {
            $class = $namespace .'\\' . $type;
            $this->verifyRenderingMethod($class);
            $views[self::getKey($type)] = $type;
        }
        $callback = function($first, $second) use ($order) {
            $pos1 = array_search($first, $order);
            $pos2 = array_search($second, $order);
            if ($pos2 === false && $pos1 === false) {
                return 0;
            }
            if ($pos1 === false) {
                return 1;
            }
            if ($pos2 === false) {
                return -1;
            }
            if ($pos1 > $pos2) {
                return 1;
            }
            return -1;
        };
        uasort($views, $callback);
        $pathInfo['views'] = $views;
    }

    private function getKey($type) {
        $result = null;
        $length = strlen($type);
        for ($index = 0; $index < $length; ++$index) {
            $char = $type[$index];
            $asciiCode = ord($char);
            if ($asciiCode > 64 && $asciiCode < 91) {
                if ($index !== 0) {
                    $result .= '_';
                }
                $result .= strtolower($char);
            }
            $result .= $char;
        }
        return $result;
    }

    private function verifyRenderingMethod($class) {
        $reflector = new \ReflectionClass($class);
        if (!$reflector->hasMethod('render')) {
            throw new \Exception(
                "Rendering method of view not found in '$class'"
            );
        }
        if (!$reflector->getMethod('render')->isPublic()) {
            throw new \Exception(
                "Rendering method of view not public in '$class'"
            );
        }
    }
}
