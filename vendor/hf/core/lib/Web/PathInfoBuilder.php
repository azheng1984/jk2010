<?php
namespace Hyperframework\Web;

use Hyperframework\ConfigLoader;
use Hyperframework\ClassRecognizer;

class PathInfoBuilder {
    private static $config;

    private static function getNamespace($path, $type) {
        $segments = explode('/', $path);
        $result = $type;
        foreach ($segments as $segment) {
            if ($segment === '') {
                continue;
            }
            $result .= '\\';
            foreach (explode('_', $segment) as $item) {
                $result .= ucfirst($item);
            }
        }
        return $result;
    }

    public static function run($path, $type, $options = null) {
        $namespace = self::getNamespace($path);
        $folder = $namespace;
        if (DIRECTORY_SEPARATOR !== '\\') {
            $folder = str_replace('\\', '/', $folder);
        }
        $folder = \Hyperframework\APP_ROOT_PATH . DIRECTORY_SEPARATOR . 'lib'
            . DIRECTORY_SEPARATOR . $folder;
        $namespace = \Hyperframework\APP_ROOT_NAMESPACE . '\\' . $namespace;
        $pathInfo = array();
        $viewTypes = array();
        foreach(scandir($folder) as $entry) {
            if ($entry === '.'
                || $entry === '..'
                || is_dir($path . DIRECTORY_SEPARATOR . $entry)
            ) {
                continue;
            }
            $name = ClassRecognizer::getName($entry);
            if ($name === 'Action') {
                ActionInfoBuilder::build($namespace . '\\' . $name);
            } else {
                $viewTypes[] = $name;
            }
        }
        if (count($viewNames) > 1) {
            $viewOrder = null;
            if (isset($options['view_order']) !== false) {
                $viewOrder = $options['view_order'];
            }
            ViewInfoBuilder::run(
                $namespace, $viewTypes, $viewOrder, $pathInfo
            );
        }
        return $pathInfo;
    }
}
