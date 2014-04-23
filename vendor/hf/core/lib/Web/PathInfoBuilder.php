<?php
namespace Hyperframework\Web;

use Hyperframework\ConfigLoader;

class PathInfoBuilder {
    private static $config;

    public static function build($path, $namespace) {
        $config = ConfigLoader::load();
        if (isset($config['default_view']) === false) {
            $defaultView = $config['default_view'];
        } else {
            $defaultView = array('Html', 'Xml', 'Json');
        }
        $cache = array('namespace' => $namespace);
        foreach(scandir($path) as $entry) {
            if ($entry === '.' || $entry === '..' || is_dir($path . $entry)) {
                continue;
            }
            $classRecognizer = new ClassRecognizer;
            $class = $classRecognizer->getClass($namespace . '\\' .$entry);
            $fullName = $namespace . '\\' . $class;
            if ($entry === 'Action') {
                $cache['action'] = ActionInfoBuilder::build($namespace);
            } else {
                $cache['views'][$class] = ViewInfoBuilder::build(
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

    private static function getConfig() {
        if (self::$config === null) {
            self::$config = ConfigLoader::load(
                'path_info_builder.php',
                'hyperframework.path_info_builder.config_path'
            );
        }
        return self::$config;
    }
}
