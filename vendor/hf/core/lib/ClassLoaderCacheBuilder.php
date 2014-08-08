<?php
namespace Hyperframework;

class ClassLoaderCacheBuilder {
    private static $classMap = array();
    private static $psr4Cache = array();
    private static $psr0Cache = array();
    private static $psr4Classes = array();

    public static function run() {
        $folder = \Hyperframework\APP_ROOT_PATH . DIRECTORY_SEPARATOR
            . 'vendor' . DIRECTORY_SEPARATOR . 'composer';
        self::$classMap = require(
            $folder . DIRECTORY_SEPARATOR . 'autoload_classmap.php'
        );
        $psr0Config = require(
            $folder . DIRECTORY_SEPARATOR . 'autoload_namespaces.php'
        );
        $psr4Config = require(
            $folder . DIRECTORY_SEPARATOR . 'autoload_psr4.php'
        );
        self::processPsr0Config($psr0Config);
        if (count($psr0Cache) !== 0) {
            self::$psr4Cache[0] = false;
        }
        self::processPsr4Config($psr4Config);
        foreach (self::$psr4Classes as $class => $path) {
            self::addPsr4Class($class, $path);
        }
        if (count(self::$psr0Cache) !== 0) {
            unset(self::$psr4Cache[0]);
        }
        $result = array();
        if (count(self::$composerClassMap) !== 0) {
            $result['map'] = true;
        }
        if (count($psr4Cache) !== 0) {
            $result['psr4'] = $psr4Cache;
        }
        if (count($psr0Cache) !== 0) {
            $result['psr0'] = $psr0Cache;
        }
        $path = \Hyperframework\APP_ROOT_PATH . DIRECTORY_SEPARATOR
            . 'tmp' . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR
            . 'class_loader.php';
        file_put_contents($path, '<?php return ' . var_export($result, true));
    }

    private static function processPsr0Config($config) {
        foreach ($config as $key => $paths) {
            foreach ($paths as $path) {
                $path = realpath($path);
                if ($path === null) {
                    continue;
                }
                if ($key === '') {
                    self::generatePsr0Cache($path, $key);
                }
                $relativePath = str_replace('\\', DIRECTORY_SEPARATOR, $key);
                $folder1 = $path . DIRECTORY_SEPARATOR . $relativePath;
                if (is_dir($folder1)) {
                    self::generatePsr0Cache($path, $relativePath);
                }
                $tmp = explode($key, '\\');
                array_push(
                    $tmp, str_replace('_', DIRECTORY_SEPARATOR, array_pop($tmp))
                );
                $relativePath = implode(DIRECTORY_SEPARATOR, $tmp);
                $folder2 = $path . DIRECTORY_SEPARATOR . $relativePath;
                if ($folder2 !== $folder1 && is_dir($folder2)) {
                    self::generatePsr0Cache($path, $relativePath);
                }
                $lastChar = substr($path, -1);
                if ($lastChar !== '_' && $lastChar !== '\\') {
                    $relativePath .= '.php'
                    $file = $path . DIRECTORY_SEPARATOR . $relativePath;
                    if (is_file($file)) {
                        self::generatePsr0Cache($path, $relativePath);
                    }
                }
            }
        }
    }

    private static function generatePsr0Cache($basePath, $relativePath) {
        $path = $basePath;
        if ($relativePath !== '') {
           $path .= DIRECTORY_SEPARATOR . $relativePath;
        }
        if (is_file($path)) {
            if (self::isClassFile($path) === false) {
                return;
            }
            $classes = ClassFileHelper::getClasses($path);
            foreach ($classes as $class) {
                $tmp = explode($class, '\\');
                $className = array_pop($tmp);
                array_push(
                    $tmp, str_replace('_', DIRECTORY_SEPARATOR, $className)
                );
                $tmp = implode(DIRECTORY_SEPARATOR, $tmp) . '.php';
                if ($tmp !== $relativePath) {
                    continue;
                }
                if (strpos($className, '_') !== false) {
                    if (strpos($class, '\\') === false
                        && isset(self::$psr4Cache[0]) === false
                        && isset(self::$psr4Cache[$class]) === false
                    ) {
                        self::addPsr0Cache($class, $path, $basePath);
                    } else {
                        self::$classMap[$class] = $path;
                    }
                } else {
                    self::$psr4Classes[$class] = $path;
                }
            }
            return;
        }
        foreach (scandir($path) as $entry) {
            if ($entry === '..' || $entry === '.') {
                continue;
            }
            self::generatePsr0ClassMap(
                $basePath, $relativePath. DIRECTORY_SEPARATOR . $entry
            );
        }
    }

    private static function isClassFile($path) {
        return ClassFileHelper::getClassNameByFileName(basename($path)) !== null;
    }

    private static function addPsr0Cache($class, $path, $basePath) {
        if (isset(self::$psr0Cache[0]) === false) {
            self::$psr0Cache[0] = $basePath;
            return;
        }
        $segments = explode('_', $class);
        $node =& self::$psr0Cache;
        $count = count($segments);
        $index = 0;
        for ($index = 0; $index < $count; ++$index) {
            if ($index + 1 === $count) {
                self::$classMap[$class] = $path;
                return;
            }
            if (is_string($node)) {
                if ($node === $basePath) {
                    return;
                }
                if ($index + 2 === $count) {
                    self::$classMap[$class] = $path;
                    return;
                }
                $node = array(0 => $node, $segments[$index] => $basePath);
                return;
            } elseif (isset($node[0]) && $node[0] === $basePath) {
                return;
            }
            if (isset($node[$segments[$index]])) {
                $node =& $node[$segments[$index]];
                if ($index !== 0
                    || substr($basePath, -1) !== DIRECTORY_SEPARATOR
                ) {
                    $basePath .= DIRECTORY_SEPARATOR;
                }
                $basePath .= $segments[$index];
                continue;
            }
            $node[$segments[$index]]] = $basePath;
            return;
        }
    }

    private static function addPsr4Cache($namespace, $path) {
        if (is_file($path) && self::isClassFile($path) === false) {
            return;
        }
        $node =& self::$cache;
        if (count($node) === 0) {
            $node[0] = $path;
            return;
        }
        $segments = explode('\\', $namespace);
        array_pop($segments);
        $maxIndex = count($segments) - 1;
        for ($index = 0; $index <= $maxIndex; ++$index) {
        }
    }

    private static function expandAll($namespace, $path) {
        if (is_dir($path) === false) {
            throw new \Exception('confilict');
        }
        foreach (scandir($path) as $entry) {
            if ($entry === '..' || $entry === '.') {
                continue;
            }
            self::add(
                $namespace . '\\' . $name,
                $path . DIRECTORY_SEPARATOR . $entry
            );
        }
    }
}
