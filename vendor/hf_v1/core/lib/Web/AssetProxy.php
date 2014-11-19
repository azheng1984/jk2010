<?php
namespace Hyperframework\Web;

use Hyperframework\Config;
use Hyperframework\FullPathRecognizer;

class AssetProxy {
    public static function run() {
        $path = RequestPath::get();
        if (Config::get('hyperframework.web.asset.enable_versioning') !== false) {
            $segments = explode('.', $path);
            $amount = count($segments);
            if ($amount < 3) {
                throw new NotFoundException;
            }
            $version = $segments[$amount - 2];
            if (AssetCacheVersion::get($path) !== $version) {
                throw new NotFoundException;
            }
            unset($segments[$amount - 2]);
            $path = implode('.', $segments);
        }
        $prefix = AssetPathPrefix::get();
        $path = substr($path, strlen($prefix) + 1);
        $file = self::searchFile($path);
        if ($file === null) {
            throw new NotFoundException;
        }
        echo AssetFilterChain::run($file);
    }

    public static function searchFile($path) {
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

    public static function getIncludePaths() {
        $paths =  \Hyperframework\ConfigFileLoader::loadPhp(
            'hyperframework.web.asset.include_paths.config_path',
            'asset' . DIRECTORY_SEPARATOR . 'include_paths.php',
            true
        );
        if ($paths === null) {
            return array('assets');
        }
        return $paths;
    }
}
