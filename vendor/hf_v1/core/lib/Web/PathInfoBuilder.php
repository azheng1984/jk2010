<?php
namespace Hyperframework\Web;

use Hyperframework\ConfigLoader;
use Hyperframework\ClassFileHelper;

class PathInfoBuilder {
    private static $config;

    public static function run($path, $type, $options) {
        $namespace = self::convertToNamespace($path, $type);
        return self::runByNamespace($namespace, $type, $options);
    }

    public static function runByNamespace($namespace, $type, $options) {
        $folder = $namespace;
        if (DIRECTORY_SEPARATOR !== '\\') {
            $folder = str_replace('\\', '/', $folder);
        }
        $folder = \Hyperframework\APP_ROOT_PATH . DIRECTORY_SEPARATOR . 'lib'
            . DIRECTORY_SEPARATOR . $folder;
        $namespace = \Hyperframework\APP_ROOT_NAMESPACE . '\\' . $namespace;
        $pathInfo = array();
        $viewTypes = array();
        if (is_dir($folder) === false) {
            throw new NotFoundException;
        }
        foreach(scandir($folder) as $entry) {
            if ($entry === '.'
                || $entry === '..'
                || is_dir($folder . DIRECTORY_SEPARATOR . $entry)
            ) {
                continue;
            }
            $name = ClassFileHelper::getClassNameByFileName($entry);
            if ($name === null) {
                continue;
            }
            if ($name === 'Action') {
                ActionInfoBuilder::run($namespace . '\\' . $name, $pathInfo);
            } else {
                $viewTypes[] = $name;
            }
        }
        if (count($viewTypes) !== 0) {
            $viewOrder = null;
            if (isset($options['view_order']) !== false) {
                $viewOrder = $options['view_order'];
            }
            ViewInfoBuilder::run(
                $namespace, $viewTypes, $viewOrder, $pathInfo
            );
        }
        $pathInfo['namespace'] = $namespace;
        return $pathInfo;
    }

    private static function convertToNamespace($path, $type) {
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
}
