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
        //do: 如果 default node 的所有子路经都被封死，删除 default node 的默认值(default node 失效)
    }

    private static function add($namespace, $path) {
        $segments = explode('\\', $namespace);
        array_pop($segments);
        $parent =& $cache;
        $maxIndex = count($segments) - 1;
        $defaultNode = null;
        $defaultNodeIndex = null;
        for ($index = 0; $index <= $maxIndex; ++$index) {
            if (isset($parent[$segment]) === false) {
                if ($index !== $maxIndex) {
                    $parent[$segment] = array();
                    $parent =& $parent[$segment];
                    continue;
                }
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
                break;
            }
            if ($index === $maxIndex) {
                if (is_string($parent[$segment]) || isset($parent[$segment[0]])) {
                    //展开所有后重新插入，因为不可能有两个默认路径
                    break;
                }
                $parent[$segment][0] = $path;
                //检查所有带路径数据的子节点
                //如果存在，当前路径显式插入冲突位置（由于重复插入路径，递归需加判断）
                break;
            }
            if (isset($parent[$segment][0])) {
                $defaultNode =& $parent[$segment];
                $defaultNodeIndex = $index;
            }
            $parent =& $parent[$segment];
        }
    }
}
