<?php
namespace Hyperframework;

class ClassLoaderCacheBuilder {
    private static $classMap = array();
    private static $psr4Cache = array();
    private static $psr4Classes = array();
    private static $psr0Cache = array();
    private static $psr0CacheFlagNodes = array();
    private static $psr0Classes = array();
    private static $psr0ClassMap = array();

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
        $hasPsr0Cache = count($psr0Cache) !== 0;
        if ($hasPsr0Cache) {
            self::$psr4Cache[0] = false;
        }
        self::processPsr4Config($psr4Config);
        self::addPsr4Classes();
        if ($hasPsr0Cache) {
            unset(self::$psr4Cache[0]);
        }
        foreach (self::$psr0ClassMap as $class => $path) {
            if (isset(self::$classMap[$class]) === false) {
                self::$classMap[$class] = $path;
            }
        }
        $result = array();
        if (count(self::$composerClassMap) !== 0) {
            $result['map'] = self::$classMap;
        }
        if (count(self::$psr4Cache) !== 0) {
            $result['psr4'] = self::$psr4Cache;
        }
        if (count(self::$psr0Cache) !== 0) {
            $result['psr0'] = self::$psr0Cache;
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
                    self::generatePsr0ClassMap($key, $path, $key);
                }
                $relativePath = str_replace('\\', DIRECTORY_SEPARATOR, $key);
                $folder1 = $path . DIRECTORY_SEPARATOR . $relativePath;
                if (is_dir($folder1)) {
                    self::generatePsr0ClassMap($key, $path, $relativePath);
                }
                $tmp = explode($key, '\\');
                array_push(
                    $tmp, str_replace('_', DIRECTORY_SEPARATOR, array_pop($tmp))
                );
                $relativePath = implode(DIRECTORY_SEPARATOR, $tmp);
                $folder2 = $path . DIRECTORY_SEPARATOR . $relativePath;
                if ($folder2 !== $folder1 && is_dir($folder2)) {
                    self::generatePsr0ClassMap($key, $path, $relativePath);
                }
                $lastChar = substr($key, -1);
                if ($lastChar !== '_' && $lastChar !== '\\') {
                    $relativePath .= '.php'
                    $file = $path . DIRECTORY_SEPARATOR . $relativePath;
                    if (is_file($file)) {
                        self::generatePsr0ClassMap($key, $path, $relativePath);
                    }
                }
            }
        }
        self::generatePsr0Cache();
    }

    private static function generatePsr0ClassMap(
        $classPrefix, $basePath, $relativePath
    ) {
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
                if (strncmp($classPrefix, $class, strlen($classPrefix)) !== 0) {
                    continue;
                }
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
                    if (strpos($class, '\\') === false) {
                        self::$psr0Classes[$class] = $basePath;
                    } else {
                        self::$psr0ClassMap[$class] = $path;
                    }
                } else {
                    if (strpos($class, '\\') === false) {
                        self::$psr0ClassMap[$class] = $path;
                    } else {
                        self::$psr4Classes[$class] = $path;
                    }
                }
            }
            return;
        }
        foreach (scandir($path) as $entry) {
            if ($entry === '..' || $entry === '.') {
                continue;
            }
            $tmp = $relativePath;
            if ($relativePath === ''
                || substr($relativePath, -1) !== DIRECTORY_SEPARATOR
            ) {
                $tmp .= DIRECTORY_SEPARATOR;
            }
            self::generatePsr0ClassMap($classPrefix, $basePath, $tmp . $entry);
        }
    }

    private static function generatePsr0Cache() {
        if (count(self::$psr0Classes) === 0) {
            return;
        }
        uksort(self::$psr0Classes, function($a, $b) {
            $x = substr_count($a, '_');
            $y = substr_count($b, '_');
            if ($x === $y) {
                return 0;
            }
            if ($x > $y) {
                return 1;
            }
            return -1;
        });
        foreach (self::$psr0Classes as $class => $basePath) {
            self::addPsr0Cache($class, $basePath);
        }
    }

    private static function addPsr0Cache($class, $basePath) {
        if (isset(self::$psr4Cache[$class])) {
            if (isset(self::$classMap[$class])) {
                return;
            }
            $tmp = str_replace('_', DIRECTORY_SEPARATOR, $class) . '.php';
            self::$classMap[$class] = $basePath . DIRECTORY_SEPARATOR . $tmp;
            return;
        }
        $segments = explode('_', $class);
        $flagNode =& self::$psr0CacheFlagNodes;
        $node =& self::$psr0Cache;
        $cacheValue = $basePath;
        $lastCacheValue =& $cacheValue;
        $cacheKey = 0;
        $hasCacheNode = true;
        $count = count($segments);
        for ($index = 0; $index < $count; ++$index) {
            if ($index + 1 === $count) {
                if (isset(self::$classMap[$class])) {
                    return;
                }
                $tmp = explode($class, '\\');
                $className = array_pop($tmp);
                array_push(
                    $tmp, str_replace('_', DIRECTORY_SEPARATOR, $className)
                );
                $tmp = implode(DIRECTORY_SEPARATOR, $tmp) . '.php';
                self::$classMap[$class] = $basePath
                    . DIRECTORY_SEPARATOR . $tmp;
                return;
            }
            if ($hasCacheNode && is_string($node)) {
                if ($node === $basePath) {
                    return;
                }
            } elseif ($hasCacheNode && isset($node[0]) && $node[0] === $basePath) {
                return;
            }
            if ($hasCacheNode) {
                $cacheKey = $segments[$index];
            } else {
                $lastCacheValue = array($segments[$index] => $basePath);
                $lastCacheValue =& $lastCacheValue[$segments[$index]];
            }
            if (isset($flagNode[$segments[$index]])) {
                $flagNode =& $flagNode[$segments[$index]];
                if ($hasCacheNode && isset($node[$cacheKey])) {
                    $node =& $node[$segments[$index]];
                } else {
                    $hasCacheNode = false;
                }
            } else {
                if (isset(self::$psr0Cache[0]) === false) {
                    self::$psr0Cache[0] = $cacheValue;
                } elseif (is_string($node)) {
                    $node = array(0 => $node, $cacheKey => $cacheValue);
                } else {
                    $node[$cacheKey] = $cacheValue;
                }
                while ($index < $count) {
                    if (isset($flagNode[$segments[$index]]) === false) {
                        $flagNode[$segments[$index]] = array();
                    }
                    $flagNode =& $flagNode[$segments[$index]];
                    ++$index;
                }
            }
        }
    }

    private static function processPsr4Config($config) {
        if (count($config) === 0) {
            return;
        }
        uksort(self::$config, function($a, $b) {
            if ($a === '') {
                return -1;
            }
            if ($b === '') {
                return 1;
            }
            $x = substr_count($a, '\\');
            $y = substr_count($b, '\\');
            if ($x === $y) {
                return 0;
            }
            if ($x > $y) {
                return 1;
            }
            return -1;
        });
        foreach ($config as $key => $paths) {
            foreach ($paths as $path) {
                $path = realpath($path);
                if ($path === null) {
                    continue;
                }
                self::addPsr4Cache($key, $path);
            }
        }
    }

    private static function addPsr4Classes() {
        if (count(self::$psr4Classes) === 0) {
            return;
        }
        uksort(self::$psr4Classes, function($a, $b) {
            if ($a === '') {
                return -1;
            }
            if ($b === '') {
                return 1;
            }
            $x = substr_count($a, '\\');
            $y = substr_count($b, '\\');
            if ($x === $y) {
                return 0;
            }
            if ($x > $y) {
                return 1;
            }
            return -1;
        });
        $psr4CacheFlagNodes = self::$psr4Cache;
        foreach (self::$psr4Classes as $class => $basePath) {
            $skipFlagNodeCheck = false;
            $cacheValuePath = '';
            $path = $basePath;
            $segments = explode('\\', $class);
            $flagNode =& $psr4CacheFlagNodes;
            $node =& self::$psr4Cache;
            $cacheValue = $basePath;
            $lastCacheValue =& $cacheValue;
            $cacheKey = 0;
            $hasCacheNode = true;
            $count = count($segments);
            for ($index = 0; $index < $count; ++$index) {
                if ($index + 1 === $count) {
                    if (isset(self::$classMap[$class])) {
                        return;
                    }
                    $path = $basePath
                        . str_replace('\\', DIRECTORY_SEPARATOR, $class)
                        . '.php';
                    self::$classMap[$class] = $path;
                    return;
                }
                if ($hasCacheNode && is_string($node)) {
                    if ($node === $path) {
                        return;
                    }
                } elseif ($hasCacheNode && isset($node[0]) && $node[0] === $path) {
                    return;
                }
                if ($hasCacheNode) {
                    $cacheKey = $segments[$index];
                } else {
                    $lastCacheValue = array($segments[$index] => $basePath);
                    $lastCacheValue =& $lastCacheValue[$segments[$index]];
                }
                if ($skipFlagNodeCheck === true
                    && isset($flagNode[$segments[$index]])
                ) {
                    $flagNode =& $flagNode[$segments[$index]];
                    if ($hasCacheNode && isset($node[$cacheKey])) {
                        $node =& $node[$segments[$index]];
                    } else {
                        $hasCacheNode = false;
                        $cacheValuePath .= DIRECTORY_SEPARATOR . $segments[$index];
                    }
                } else {
                    $defaultPath = null;
                    if (is_string[$node]) {
                        $defaultPath = $node;
                    } elseif (isset($node[0])) {
                        $defaultPath = $node[0];
                    }
                    if ($defaultPath !== null && is_dir(
                        $defaultPath . $cacheValuePath
                            . DIRECTORY_SEPARATOR . $segments[$index]
                    )) {
                        $hasCacheNode = false;
                        $skipFlagNodeCheck = true;
                        $cacheValuePath .= DIRECTORY_SEPARATOR . $segments[$index];
                        continue;
                    }
                    if (isset(self::$psr4Cache[0]) === false) {
                        self::$psr4Cache[0] = $cacheValue;
                    } elseif (is_string($node)) {
                        $node = array(0 => $node, $cacheKey => $cacheValue);
                    } else {
                        $node[$cacheKey] = $cacheValue;
                    }
                    while ($index < $count) {
                        if (isset($flagNode[$segments[$index]]) === false) {
                            $flagNode[$segments[$index]] = array();
                        }
                        $flagNode =& $flagNode[$segments[$index]];
                        ++$index;
                    }
                }
            }
        }
    }

    private static function addPsr4Cache($namespace, $path) {
        if (is_file($path)) {
            if (self::isClassFile($path)
                && isset(self::$classMap[$namespace]) === false
            ) {
                self::$classMap[$namespace] = $path;
            }
            return;
        }
        $namespace = trim($namespace, '\\');
        if ($namespace === '') {
            if (isset(self::$psr4Cache[0]) === false) {
                self::$psr4Cache[0] = $path;
            } else {
                self::expandAll($namespace, $path);
            }
            return;
        }
        $segments = explode('\\', $namespace);
        $count = count($segments);
        $cacheValuePath = $path;
        $node =& self::$psr4Cache;
        $defaultNode = null;
        if (is_string($node) || isset($node[0])) {
            $defaultNode = $node;
        }
        $cacheKey = 0;
        $cacheValue = $path;
        $lastCacheValue =& $cacheValue;
        $hasCacheNode = true;
        for ($index = 0; $index < $count; ++$index) {
            if ($hasNode && isset($node[$segments[$index]])) {
                $node =& $node[$segments];
                if (is_string($node) && $node === $path) {
                    return;
                } elseif (isset($node[0]) && $node[0] === $path) {
                    return;
                }
                if (is_string($node) || isset($node[0])) {
                    $defaultNode = $node;
                }
                continue;
            } else {
                $hasNode = false;
            }
            $lastCacheValue = array($segments[$index] => $path);
            $lastCacheValue =& $lastCacheValue[$segments[$index]];
            if ($defaultNode !== null) {
                $defaultPath = $defaultNode;
                if (is_array($defaultNode)) {
                    $defaultPath = $defaultNode[0];
                }
                $cacheValuePath .= DIRECTORY_SEPARATOR . $segments[$index];
                if (is_dir($defaultPath . $cacheValuePath)) {
                    continue;
                }
            }
            $node[$cacheKey] = $cacheValue;
        }
        self::expandAll($namespace, $path);
    }

    private static function expandAll($namespace, $path) {
        foreach (scandir($path) as $entry) {
            if ($entry === '..' || $entry === '.') {
                continue;
            }
            $path = $path . DIRECTORY_SEPARATOR . $entry;
            if (is_file($path)) {
                if (self::isClassFile($path)) {
                    self::addPsr4Cache(
                        $namespace . '\\' . ClassFileHelper::getClassNameByFileName($entry),
                        $path . DIRECTORY_SEPARATOR . $entry,
                    );
                }
            }
            self::addPsr4Cache(
                $namespace . '\\' . $entry,
                $path . DIRECTORY_SEPARATOR . $entry,
            );
        }
    }

    private static function isClassFile($path) {
        return ClassFileHelper::getClassNameByFileName(basename($path)) !== null;
    }
}
