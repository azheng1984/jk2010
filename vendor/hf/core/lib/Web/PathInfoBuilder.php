<?php
namespace Hyperframework\Web;

use Hyperframework\ConfigLoader;

class PathInfoBuilder {
    private $defaultView;
    private $cache;

    public static function build($path, $namespace) {
        if (isset($options['default_view']) === false) {
            self::$defaultView = $options['default_view'];
        } else {
            self::$defaultView = array('Html', 'Xml', 'Json');
        }
        $cache = null;
        foreach(scandir($path) as $entry) {
            if ($entry === '.' || $entry === '..') {
                continue;
            }
            $classRecognizer = new ClassRecognizer;
            $class = $classRecognizer->getClass($entry);
            if ($class === null) {
                return;
            }
            $fullName = $namespace . '\\' . $class;
            if ($class === 'Action') {
                ActionInfoBuilder::build($cache, $fullName);
            } else {
                ViewInfoBuilder::build($cache, $fullName);
            }
        }
        return $cache;
    }
}
