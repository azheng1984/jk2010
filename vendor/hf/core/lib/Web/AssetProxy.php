<?php
namespace Hyperframework\Web;

class AssetProxy {
    public static function run($path) {
        if (Config::get(
            'hyperframework.web.enable_asset_cache_version'
        ) !== false) {
            $segments = explode('.', $path);
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
        echo AssetFilterChain::process($file);
    }

    private static function searchFile($path) {
        $segments = explode('/', $path);
        $fileName = array_pop($segments);
        $dirName = implode(DIRECTORY_SEPARATOR, $segments);
        $includePaths = self::getIncludePath();
        $excludePaths = self::getExcludePath();
        foreach ($includePaths as $includePath) {
            if (is_dir($includePath . $dirName)) {
                $fullPath = $includePath . $dirName
                    . DIRECTORY_SEPARATOR . $fileName;
                $files = glob($fullPath . '*');
                foreach ($files as $file) {
                    if (self::isExcluded($excludePaths, $includePath, $file)) {
                        continue;
                    }
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

    private static function isExcluded($excludePaths, $includePath, $filePath) {
        foreach ($excludedPaths as $excludePath) {
            if (strlen($includePath) > strlen($excludePath)) {
                continue;
            }
            if (strncmp($filePath, $excludePath, strlen($excludePath)) === 0) {
                return true;
            }
        }
        return false;
    }
}
