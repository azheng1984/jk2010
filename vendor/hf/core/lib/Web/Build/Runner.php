<?php
namespace Hyperframework\Web\Build;

use Hyperframework\EnvironmentBuilder;
use Hyperframework\ClassRecognizer;
use Hyperframework\Cli\ExceptionHandler;

use Hyperframework\Web\ActionInfoBuilder;
use Hyperframework\Web\ViewInfoBuilder;
use Hyperframework\Web\AssetCacheBuilder;

class Runner {
    public static function run($rootNamespace, $rootPath) {
        static::initialize($rootNamespace, $rootPath);
        self::buildPathInfoCache('App');
        self::buildPathInfoCache('ErrorApp');
        AssetCacheBuilder::run();
        //ClassLoaderCacheBuilder::run();
    }

    private static function buildPathInfoCache($type) {
        $root = \Hyperframework\APP_ROOT_PATH
            . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . $type;
        $pathInfo = array();
        self::get('/', $root, \Hyperframework\APP_ROOT_NAMESPACE .'\\' . $type, $pathInfo);
        var_dump($pathInfo);
    }

    private static function get($path, $folder, $namespace, &$pathInfo) {
        $viewTypes = array();
        $pathInfo[$path] = array();
        foreach (scandir($folder) as $entry) {
            if ($entry === '.' || $entry === '..') {
                continue;
            }
            if (is_dir($folder . DIRECTORY_SEPARATOR . $entry)) {
                $tmp = $path;
                if ($path !== '/') {
                    $tmp .= '/';
                }
                self::get(
                    $tmp . self::convertToPath($entry),
                    $folder . DIRECTORY_SEPARATOR . $entry,
                    $namespace .'\\'. $entry,
                    $pathInfo
                );
                continue;
            }
            $name = ClassRecognizer::getName($entry);
            if ($name === null) {
                continue;
            }
            if ($name === 'Action') {
                ActionInfoBuilder::run($namespace . '\\' . $name, $pathInfo[$path]);
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
                $namespace, $viewTypes, $viewOrder, $pathInfo[$path]
            );
        }
        if (count($pathInfo[$path]) !== 0) {
            $pathInfo[$path]['namespace'] = $namespace;
        } else {
            unset($pathInfo[$path]);
        }
    }

    private static function convertToPath($namespace) {
        return strtolower($namespace);
    }

    protected static function initialize($rootNamespace, $rootPath) {
        require dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR
            . 'EnvironmentBuilder.php';
        EnvironmentBuilder::run($rootNamespace, $rootPath);
        ExceptionHandler::run();
    }
}
