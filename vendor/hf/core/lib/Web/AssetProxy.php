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
               throw new NotFoundException; 
            }
            $version = $segments[$amount - 2];
            unset($segments[$amount - 2]);
            $path = implode('.', $segments);
            if (AssetCacheVersion::get($path) === $segments[$amount - 2]) {
                throw new NotFoundException; 
            }
        }
        $file = self::searchFile($path);
        $realPath = null;
        $result = AssetFilterChain::process($file, $realPath);
        if ($realPath !== $path) {
            throw new NotFoundException;
        }
        echo $result;
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
                    $suffix = substr($file, strlen($fullPath) - 1);
                    if ($suffix === '') {
                        return $file;
                    }
                    $filterTypes = explode('.', $suffix);
                    foreach ($types as $type) {
                        if (AssetFilterChain::isValidFilterSuffix($type) === false) {
                            break;
                        }
                    }
                    return $file;
                }
            }
        }
        throw new NotFoundException;
    }
}
