<?php
namespace Hyperframework\Web;

class AssetProxy {
    public static function run($path) {
        if (Config::get('hyperframework.web.enable_asset_cache_version')
            !== false
        ) {
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
        if ($file === null) {
            throw new NotFoundException; 
        }
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
        foreach (self::getIncludePaths() as $includePath) {
            if (is_dir($includePath . $dirName)) {
                $fullPath = $includePath . $dirName
                    . DIRECTORY_SEPARATOR . $fileName;
                $files = glob($fullPath . '*');
                foreach ($files as $file) {
                    if (AssetFilterChain::removeFilterExtensions($file)
                        === $file) {
                        return $file;
                    }
                }
            }
        }
    }

    private static function getIncludePaths() {
        $paths =  \Hyperframework\PhpConfigDataLoader::load(
            'hyperframework.web.asset_cache.include_paths_config_path',
            'asset_cache' . DIRECTORY_SEPARATOR . 'include_paths.php',
            true
        );
        if ($paths === null) {
            return array();
        }
    }
}
