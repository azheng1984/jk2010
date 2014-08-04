<?php
namespace Hyperframework\Web;

use Hyperframework\Config;
use Hyperframework\FullPathRecognizer;

class AssetProxy {
    public static function run() {
        $path = RequestPath::get();
        if (Config::get('hyperframework.asset.enable_versioning') !== false) {
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
        echo AssetFilterChain::run($file);
    }

    private static function searchFile($path) {
        $prefix = AssetPathPrefix::get();
        $path = substr($path, strlen($prefix) + 1);
        $segments = explode('/', $path);
        $fileName = array_pop($segments);
        $folder = implode(DIRECTORY_SEPARATOR, $segments);
        foreach (self::getIncludePaths() as $includePath) {
            $folderFullPath = $includePath;
            if ($folder !== '') {
                $folderFullPath .=  DIRECTORY_SEPARATOR . $folder;
            } 
            if (FullPathRecognizer::isFull($folderFullPath) === false) {
                $folderFullPath = \Hyperframework\APP_ROOT_PATH
                    . DIRECTORY_SEPARATOR . $folderFullPath;
            }
            if (is_dir($folderFullPath)) {
                $fileFullPath = $folderFullPath
                    . DIRECTORY_SEPARATOR . $fileName;
                $files = glob($fileFullPath . '*');
                foreach ($files as $file) {
                    $tmp = explode('/', $file);
                    $tmp = end($tmp);
                    if (AssetFilterChain::removeInternalFileNameExtensions($tmp)
                        === $fileName) {
                        return $file;
                    }
                }
            }
        }
    }

    private static function getIncludePaths() {
        $paths =  \Hyperframework\ConfigFileLoader::loadPhp(
            'hyperframework.asset.include_paths.config_path',
            'asset' . DIRECTORY_SEPARATOR . 'include_paths.php',
            true
        );
        if ($paths === null) {
            return array('assets');
        }
        return $paths;
    }
}
