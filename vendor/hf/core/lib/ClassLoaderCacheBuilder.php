<?php
namespace Hyperframework;

class ClassLoaderCacheBuilder {
    private static $cache;
    private static $composerClassMap = array();

    public static function run() {
        $folder = \Hyperframework\APP_ROOT_PATH . DIRECTORY_SEPARATOR
            . 'vendor' . DIRECTORY_SEPARATOR . 'composer';
        self::$composerClassMap = require($folder . DIRECTORY_SEPARATOR . 'autoload_classmap.php');
        $psr0 = require($folder . DIRECTORY_SEPARATOR . 'autoload_namespaces.php');
        $psr4 = require($folder . DIRECTORY_SEPARATOR . 'autoload_psr4.php');
        $psr4Cache = array();
        self::$cache =& $psr4Cache;
        foreach ($psr4 as $namespace => $paths) {
            foreach ($paths as $path) {
                $realPath = realpath($path);
                if ($realPath !== false) {
                    self::add(rtrim($namespace, '\\'), $realPath);
                }
            }
        }
        $psr0Cache = array();
        self::$cache =& $psr0Cache;
        foreach ($psr0 as $key => $paths) {
            if (self::isPsr4($psr4Cache, $key)) {
                foreach ($paths as $path) {
                    self::generatePsr0ClassMap($path);
                }
                continue;
            }
            $namespace = $key;
            if (substr($key, -1) !== '\\') {
                $tmp = explode('\\', $key);
                array_push($tmp, str_replace('_', '\\', array_pop($tmp)));
                $namespace = implode('\\', $tmp);
            }
            foreach ($paths as $path) {
                self::add(rtrim($namespace, '\\'), realpath($path));
            }
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

    private static function isClassFile($path) {
        $tmp = explode(DIRECTORY_SEPARATOR, $path);
        return ClassRecognizer::getName(array_pop($tmp)) !== null;
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

    private static function getClasses($path) {
        $code = file_get_contents($path);
        $classes = array();
        $namespace = '';
        $tokens = token_get_all($code);
        $count = count($tokens);
        for ($index = 0; $index < $count; $index++) {
            if (isset($tokens[$index][0]) === false) {
                continue;
            }
            if ($tokens[$index][0] === T_NAMESPACE) {
                $namespace = '';
                ++$index;
                while ($index < $count) {
                    if (isset($tokens[$index][0]) && $tokens[$index][0] === T_STRING) {
                        $namespace .= "\\" . $tokens[$index][1];
                    } elseif ($tokens[$index] === '{' || $tokens[$index]=== ';') {
                        break;
                    }
                    ++$index;
                }
            } elseif ($tokens[$index][0] === T_CLASS) {
                while ($index < $count) {
                    if (isset($tokens[$index][0]) && $tokens[$index][0] === T_STRING) {
                        $classes[] = $namespace . "\\" . $tokens[$index][1];
                        break;
                    }
                    ++$index;
                }
            }
        }
        return $classes;
    }

    private static function isPsr4($cache, $key) {
        $node =& $cache;
        foreach (explode('\\', rtrim($key, '\\')) as $item) {
            if (isset($node[$item])) {
                if (isset($node[$item][0]) || is_string($node[$item])) {
                    true;
                }
                $node =& $node[$item];
            } else {
                return false;
            }
        }
        return false;
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

    protected static function checkForwardConfilict(&$node, $path, $namespace) {
        foreach (scandir($path) as $entry) {
            if ($entry === '..' || $entry === '.') {
                continue;
            }
            if (isset($node[$entry])) {
                $currentPath = null;
                if (isset($node[$entry][0])) {
                    $currentPath = $node[$entry][0]; 
                } elseif (is_string($node[$entry])) {
                    $currentPath = $node[$entry];
                }
                if ($currentPath === null) {
                    self::checkForwardConfilict(
                        $node[$entry],
                        $path . DIRECTORY_SEPARATOR . $entry,
                        $namespace . '\\' . $entry
                    );
                }
                if ($currentPath !== $path . DIRECTORY_SEPARATOR . $entry) {
                    self::add(
                        $namespace . '\\' . $entry,
                        $path . DIRECTORY_SEPARATOR . $entry
                    );
                }
            }
        }
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
                } else {
                    $parent[$segment][0] = $path;
                    if ($defaultNode !== null) {
                        self::checkDefaultNode(
                            $defaultNode,
                            $segments,
                            $defaultNodeIndex,
                            $maxIndex,
                            $namespace
                        );
                    }
                    if (isset($parent[$segment][0]) === false) {
                        break;
                    }
                    self::checkForwardConfilict(
                        $parent[$segment], $path, $namespace
                    );
                    self::checkExpansion($parent[$segment]);
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
