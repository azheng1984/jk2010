<?php
namespace Hyperframework;

// todo:
// Db.php
// Db

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
                self::add(rtrim($namespace, '\\'), realpath($path));
            }
        }
        $psr0Cache = array();
        self::$cache =& $psr0Cache;
        foreach ($psr0 as $key => $paths) {
            $namespace = $key;
            if (substr($key, -1) !== '\\') {
                $tmp = explode('\\', $key);
                array_push($tmp, str_replace('_', '\\', array_pop($tmp)));
                $namespace = implode('\\', $tmp);
            }
            if (self::isPsr4($psr4Cache, $key)) {
                foreach ($paths as $path) {
                    self::generatePsr0ClassMap($path);
                }
                continue;
            }
            foreach ($item as $i) {
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
            . 'class_loader.php', 
        file_put_contents($path, var_export($result, true));
    }

    private static function generatePsr0ClassMap($path) {
        if (is_file($path)) {
            $class = self::getClasses($path);
            foreach ($class as $i) {
                if (isset(self::$composerClassMap[$i]) === false) {
                    self::$composerClassMap[$i] = $path;
                }
            }
            return;
        }
        foreach (scandir($path) as $entry) {
            if ($entry === '..' || $entry === '.') {
                continue;
            }
            self::generatePsr0ClassMap($path);
        }
    }

    public function getClasses($path) {
        $phpCode = file_get_contents($path);
        $classes = array();
        $namespace = 0;  
        $tokens = token_get_all($phpcode); 
        $count = count($tokens); 
        $dlm = false;
        for ($i = 2; $i < $count; $i++) { 
            if ((isset($tokens[$i - 2][1])
                && ($tokens[$i - 2][1] == "phpnamespace" || $tokens[$i - 2][1] == "namespace")) || 
                ($dlm && $tokens[$i - 1][0] == T_NS_SEPARATOR && $tokens[$i][0] == T_STRING)) { 
                if (!$dlm) $namespace = 0; 
                if (isset($tokens[$i][1])) {
                    $namespace = $namespace ? $namespace . "\\" . $tokens[$i][1] : $tokens[$i][1];
                    $dlm = true; 
                }   
            }       
            elseif ($dlm && ($tokens[$i][0] != T_NS_SEPARATOR)
                && ($tokens[$i][0] != T_STRING)) {
                $dlm = false; 
            } 
            if (($tokens[$i - 2][0] == T_CLASS || (isset($tokens[$i - 2][1])
                && $tokens[$i - 2][1] == "phpclass")) 
                    && $tokens[$i - 1][0] == T_WHITESPACE && $tokens[$i][0] == T_STRING) {
                $class_name = $tokens[$i][1]; 
                if (!isset($classes[$namespace])) $classes[$namespace] = array();
                $classes[$namespace][] = $class_name;
            }
        }
        return $classes;
    }

    private static function isPsr4($cache, $name) {
        $node =& $cache;
        foreach (explode('\\', trim($name, '\\')) as $item) {
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
            if (is_file($file) || is_dir($file)) {
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
