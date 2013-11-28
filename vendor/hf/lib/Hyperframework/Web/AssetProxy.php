<?php
namespace Hyperframework\Web;

class AssetProxy {
    public static function run() {
        $segments = explode('.', $_SERVER['REQUEST_URI']);
        array_shift($segments);
        //0.获取文件名，做 file_exist 查询 如果没有找到，如果有缓存，查询缓存
        //1.使用 glob 查询(可以考虑缓存)
        static::process($segments);
    }

    private static function process($extensions) {
        foreach ($extensions as $extension) {
            if ($extension === 'js' || $extension === 'css') {
                continue;
            }
            if (preg_match('/^v[0-9]+/', $extension) === 1) {
                continue;
                //$version = substr($extension, 1);
            }
        }
    }
}
