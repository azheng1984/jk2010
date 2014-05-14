<?php
namespace Hyperframework\Web;

class AssetProxy {
    public static function run() {
        $url = $_SERVER['REQUEST_URI'];
        $isAssetCacheVersionEnabled = Config::get(
            'hyperframework.web.enable_asset_cache_version'
        ) !== false;
        if ($isAssetCacheVersionEnabled) {
            $segments = explode('.', $url);
            $amount = count($segments);
            if ($amount < 3) {
                throw new Exception;
            }
            //todo 检查版本，如果不匹配，抛出 not found
            unset($segments[$amount - 2]);
            if (self::isValidFilterType($segments[$amount - 1])) {
                throw new NotFoundException;
            }
            $path = implode('.', $segments);
        }
        $file = self::getFile($path);
        static::render($file);
    }

    private static function searchFile($path) {
        $segments = explode('/', $path);
        $fileName = array_pop($segments);
        $dirName = implode(DIRECTORY_SEPARATOR, $segments);
        $rootPaths = self::getIncludePath();
        $ignorePaths = self::getIgnorePath();
        foreach ($rootPaths as $rootPath) {
            if (is_dir($rootPath . $dirName)) {
                $fullPath = $rootPath . $dirName
                    . DIRECTORY_SEPARATOR . $fileName;
                $files = glob($fullPath . '*');
                foreach ($files as $file) {
                    if (self::isExcluded($file))
                    $suffix = substr($file, strlen($fullPath) - 1);
                    if ($suffix === '') {
                        return $file;
                    }
                    $filterTypes = explode('.', $suffix);
                    foreach ($types as $type) {
                        if (static::isValidFilterSuffix($type) === false) {
                            break;
                        }
                    }
                    return $file;
                }
            }
        }
        throw new NotFoundException;
    }

    private static function render($path) {
        $segments = explode('.', $path);
        $output = file_get_contents($path);
        for (;;) {
            $filterType = array_pop($path);
            if (self::isValidFilterType($fileType) === false) {
                return;
            }
            $content = self::filte($content, $fileType);
        }
    }

    private static function filte($content, $fileType) {
        if ($fileType === 'php') {
            ob_start();
            eval('?>' . $content);
            $result = ob_get_contents();
            ob_end_clean();
            return $result;
        }
        return $content;
    }
}
