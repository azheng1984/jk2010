<?php
namespace Hyperframework\Web;

use Hyperframework\ConfigLoader;
use Hyperframework\ClassRecognizer;

class PathInfoBuilder {
    private static $config;

    public static function build($path, $namespace, $options = null) {
        $defaultView = null;
        if ($options === null || isset($options['default_view']) === false) {
            $defaultView = array('Html', 'Xml', 'Json');
        } else {
            $defaultView = $options['default_view'];
        }
        $cache = array('namespace' => $namespace);
        foreach(scandir($path) as $entry) {
            if ($entry === '.' || $entry === '..' || is_dir($path . '/' . $entry)) {
                continue;
            }
            // echo $path . '/' . $entry;
            $classRecognizer = new ClassRecognizer;
            $class = $classRecognizer->getClass($entry);
            $fullName = $namespace . '\\' . $class;
            if ($class === 'Action') {
                //$cache['action'] = ActionInfoBuilder::build($namespace);
            } else {
                $builder = new ViewInfoBuilder($defaultView);
                $cache['views'] = $builder->build(
                    $namespace, $class
                );
            }
        }
        if (count($cache['views']) > 1) {
            $callback = function($first, $second) use ($defaultView) {
                $pos1 = array_search($first, $defaultView);
                $pos2 = array_search($second, $defaultView);
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
            uksort($cache['views'], $callback);
        }
        return $cache;
    }
}
