<?php
namespace Hyperframework;

class ClassLoaderCacheBuilder {
    private static $cache = array();

    public static function run() {
        $folder = \Hyperframework\APP_ROOT_PATH . DIRECTORY_SEPARATOR
            . 'vendor' . DIRECTORY_SEPARATOR . 'composer';
        //$classMap = require($folder . DIRECTORY_SEPARATOR . 'autoload_classmap.php');
        $psr0 = require($folder . DIRECTORY_SEPARATOR . 'autoload_namespaces.php');
        $psr4 = require($folder . DIRECTORY_SEPARATOR . 'autoload_psr4.php');
        foreach ($psr0 as $key => $item) {
            if (strpos($key, '\\') === false) {
                $key = str_replace('_', '\\', $key);
            }
            if (isset($psr4[$key])) {
                $psr4[$key] = array();
            }
            foreach ($item as $i) {
                $psr4[$key][] =realpath($i . DIRECTORY_SEPARATOR . substr(
                    str_replace("\\", DIRECTORY_SEPARATOR, $key), 0, strlen($key) -1));
            }
        }
        foreach ($psr4 as $namespace => &$paths) {
            foreach ($paths as $path) {
                self::add($namespace, $path);
            }
        }
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
        $parent =& $cache;
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
