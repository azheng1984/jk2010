<?php
namespace Hyperframework;

class ClassLoaderCacheBuilder {
    public static function build($config) {
        $cache = array();
        foreach ($config as $namespace => $path) {
            if (PathTypeRecognizer::isFull($path) === false) {
                $path = APPLICATION_PATH . DIRECTORY_SEPARATOR . $path;
            }
            $segments = explode('\\', $namespace);
            $parent =& $cache;
            $amount = count($segments);
            $index = 0;
            foreach ($segments as $segment) {
                ++$index;
                if (isset($parent[$segment]) === false) {
                    if ($index !== $amount) {
                        $parent[$segment] = array();
                        $parent =& $parent[$segment];
                        continue;
                    }
                    $parent[$segment] = $path;
                    break;
                }
                if ($index === $amount) {
                    if (is_string($parent[$segment])) {
                        $parent[$segment] = array($parent[$segment], $path);
                        continue;
                    }
                    $parent[$segment][] = $path;
                    break;
                }
                if (is_string($parent[$segment])) {
                    $parent[$segment] = array($parent[$segment]);
                }
                $parent =& $parent[$segment];
            }
        }
        return $cache;
    }

    public static function append(&$cache, $config) {
    }
}
