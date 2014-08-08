<?php
namespace Hyperframework;

class ClassLoaderCacheBuilder {
    private static $classMap = array();
    private static $psr4Cache = array();
    private static $psr0Cache = array();
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
        self::generatePsr0ClassMap($psr0Config);
        self::generatePsr0Cache();
        if (count(self::$psr0Cache) === 0) {
            self::$psr0Cache = null;
        }
        self::generatePsr4Cache($psr4Config);
        $result = array();
        if (count(self::$composerClassMap) !== 0) {
            $result['map'] = true;
        }
        if (count($psr4Cache) !== 0) {
            $result['psr4'] = $psr4Cache;
        }
        if ($psr0Cache !== null) {
            $result['psr0'] = $psr0Cache;
        }
        $path = \Hyperframework\APP_ROOT_PATH . DIRECTORY_SEPARATOR
            . 'tmp' . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR
            . 'class_loader.php';
        file_put_contents($path, '<?php return ' . var_export($result, true));
    }

    private static function generatePsr0ClassMapByConfig($config) {
        foreach ($config as $key => $paths) {
            foreach ($paths as $path) {
                $path = realpath($path);
                if ($path === null) {
                    continue;
                }
                $tmp = explode($key, '\\');
                array_push(
                    $tmp, str_replace('_', DIRECTORY_SEPARATOR, array_pop($tmp))
                );
                $tmp = implode(DIRECTORY_SEPARATOR, $tmp);
                self::generatePsr0ClassMap($classPrefix, $tmp);
                self::generatePsr0ClassMap($classPrefix, $path);
                // a\b_c\
                // a\b\c\
                // a\b\c.php
                $lastChar = substr($path, -1);
                if ($lastChar !== '_' && $lastChar !== '\\') {
                }
            }
        }
    }

    private static function generatePsr0ClassMap($basePath, $relativePath = null) {
        $path = $basePath . DIRECTORY_SEPARATOR . $relativePath;
        if (is_file($path)) {
            if (self::isClassFile($path) === false) {
                return;
            }
            $classes = self::getClasses($path);
            foreach ($classes as $class) {
                $tmp = explode($class, '\\');
                array_push(
                    $tmp, str_replace('_', DIRECTORY_SEPARATOR, array_pop($tmp))
                );
                $tmp = implode(DIRECTORY_SEPARATOR, $tmp) . '.php';
                if ($tmp !== $relativePath) {
                    continue;
                }
                if (isset(self::$composerClassMap[$class]) === false) {
                    self::$composerClassMap[$class] = $path;
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

    private static function genPsr0ClassMap($) {
        return null;
    }
    

    private static function generatePsr0Cache($config) {
        foreach ($psr0Config as $key => $paths) {
            foreach ($paths as $path) {
                self::generatePsr0ClassMap($path);
            }
        }
        if (count(self::$levelOnePsr0Classes) === 0) {
            return;
        }
        $classes = array();
        foreach (self::$levelOnePsr0Classes) as $class) {
            $segments = explode('_', $class);
            $count = count($segments);
            if (isset($classes[$count]) === false) {
                $classes[$count] = array();
            }
            $classes[$count][] = $segments;
            krsort($classes);
            foreach ($classes as $items) {
                if ($items as $item) {
                    if ($psr0Cache === 0) {
                    }
                }
            }
        }
    }

    private static function generatePsr4Cache($config) {
    }

    private static function isClassFile($path) {
        return ClassFileHelper::getClassNameByFileName(basename($path)) !== null;
    }


    private static function checkDefaultNode(
        &$defaultNode, $segments, $defaultNodeindex, $maxIndex, $namespace
    ) {
        $file = $defaultNode[0];
        for ($index = $defaultNodeIndex; $index <= $maxIndex; ++$index) {
            $file .= DIRECTORY_SEPARATOR . $segments[$index];
            if ((is_file($file) && self::isClassFile($file)) || is_dir($file)) {
                self::add($namespace, $defaultNode[0]);
            }
        }
        self::checkExpansion($defaultNode);
    }

    private static function checkExpansion(&$node) {
        if (isset($node[0]) === false) {
            return;
        }
        if (self::isExpanded($node, $node[0])) {
            unset($node[0]);
        }
    }

    private static function isExpanded(&$node, $path) {
        if (is_file($path) === true) {
            return true;
        }
        foreach (scandir($path) as $entry) {
            if ($entry === '..' || $entry === '.') {
                continue;
            }
            if (isset($node[$entry]) === false) {
                return false;
            }
            if (isset($node[$entry][0]) === false) {
                $tmp = self::isExpanded(
                    $node[$entry], $path . DIRECTORY_SEPARATOR . $entry
                );
                if ($tmp === false) {
                    return false;
                }
            }
        }
        return true;
    }

    private static function add($namespace, $path) {
        if (is_file($path) && self::isClassFile($path) === false) {
            return;
        }
        $segments = explode('\\', $namespace);
        array_pop($segments);
        $parent =& self::$cache;
        $maxIndex = count($segments) - 1;
        $defaultNode = null;
        $defaultNodeIndex = null;
        for ($index = 0; $index <= $maxIndex; ++$index) {
            if (is_string($parent) || isset($parent[0])) {
                $defaultNode =& $parent;
                $defaultNodeIndex = $index - 1;
            }
            $segment = $segments[$index];
            if (is_string($parent)) {
                $parent = array($parent, $segment => null);
            } elseif ($parent === null) {
                $parent = array($segment => null);
            } elseif (isset($parent[$segment]) === false) {
                $parent[$segment] = null;
            }
            if ($index !== $maxIndex) {
                $parent =& $parent[$segment];
                continue;
            }
            if (isset($parent[$segment]) === false) {
                $parent[$segment] = $path;
                if ($defaultNode !== null) {
                    self::checkDefaultNode(
                        $defaultNode,
                        $segments,
                        $defaultNodeIndex,
                        $maxIndex,
                        $namespace
                    );
                }
            } else {
                $currentPath = null;
                if (is_string($parent[$segment])) {
                    $currentPath = $parent[$segment];
                } elseif (isset($parent[$segment][0])) {
                    $currentPath = $parent[$segment][0];
                }
                if ($currentPath !== null) {
                    if ($currentPath === $path) {
                        break;
                    }
                    self::expandAll($namespace, $path);
                }
            }
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
