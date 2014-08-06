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
            if (isset($psr4[$key])) {
                $psr4[$key] = array();
            }
            foreach ($item as $i) {
                //do: 处理下划线
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
        }
        if (is_file($file) || is_dir($file)) {
            self::add($namespace, $defaultNode[0]);
        }
        //do: 检查 default node 的所有子路经是否都被封死
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
                    //展开所有直接子节点后重新插入，因为不可能有两个默认路径
                } else {
                    $parent[$segment][0] = $path;
                    //backward default node check
                    if ($defaultNode !== null) {
                        self::checkDefaultNode(
                            $defaultNode,
                            $segments,
                            $defaultNodeIndex,
                            $maxIndex,
                            $namespace
                        )
                    }
                    //forward default node 检查所有带路径数据的子节点
                    //如果存在冲突，当前路径显式插入冲突位置(default node is conflict node)
                    //check current node's children are all listed
                    //if all listed, reset node
                }
            }
        }
    }

    private static function expandAll($namespace, $path) {
        foreach (scandir($path) as $entry) {
            if ($entry) {
            }
        }
    }
}
