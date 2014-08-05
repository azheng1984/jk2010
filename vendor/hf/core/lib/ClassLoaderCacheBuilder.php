<?php
namespace Hyperframework;

class ClassLoaderCacheBuilder {
    public static function run() {
        $folder = \Hyperframework\APP_ROOT_PATH . DIRECTORY_SEPARATOR
            . 'vendor' . DIRECTORY_SEPARATOR . 'composer';
        //$classMap = require($folder . DIRECTORY_SEPARATOR . 'autoload_classmap.php');
        $psr0 = require($folder . DIRECTORY_SEPARATOR . 'autoload_namespaces.php');
        $psr4 = require($folder . DIRECTORY_SEPARATOR . 'autoload_psr4.php');
        foreach ($psr0 as $key => $item) {
            if (isset($psr4[$key])) {
                $psr4[$key] = array();
            }
            foreach ($item as $i) {
                $psr4[$key][] =realpath($i . DIRECTORY_SEPARATOR . substr(
                    str_replace("\\", DIRECTORY_SEPARATOR, $key), 0, strlen($key) -1));
            }
        }
        $cache = array();
        foreach ($psr4 as $namespace => &$paths) {
            foreach ($paths as $path) {
                $segments = explode('\\', $namespace);
                array_pop($segments);
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
                            //confilict!
                            $parent[$segment] = self::merge(
                                $parent[$segment], $parent[$segment], $path
                            );
                            continue;
                        }
                        foreach ($parent[$segment] as $key => $value) {
                            if (is_int($key)) {
                                //confilict!
                            } elseif (is_dir($key)) {
                            }
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
        }
    }

    private static function merge(&$node, $path1, $path2) {
    }
}
