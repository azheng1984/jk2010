<?php
use Hyperframework\Config;

class Router {
    public function __construct() {
        if (Config::get('hyperframework.web.enable_asset_proxy') === true) {
            $assetPath = $this->getAssetPath($urlPath);
            if ($assetPath !== false) {
                return array('is_asset' => true, 'path' => $assetPath);
            }
        }
        return array(
            'is_asset' => false,
            'path' => $this->getApplicationPath($urlPath)
        );
    }

    protected static function getUrlPath() {
        $segments = explode('?', $_SERVER['REQUEST_URI'], 2);
        $result = $segments[0];
        if ($result === '') {
            return '/';
        }
        if ($result[0] === '#') {
            throw new NotFoundException;
        }
        return $result;
    }

    protected static function getAssetPath($urlPath) {
        $prefix = AssetCachePathPrefix::get();
        $prefixLength = strlen($prefix);
        if ($prefix === '/' || $prefixLength === 0) {
            return $urlPath;
        }
        if ($prefix[$perfixLength - 1] !== '/') {
            $prefix .= '/';
            $prefixLength += 1;
        }
        if (strncmp($urlPath, $prefix, $prefixLength) !== 0) {
            return false;
        }
        return substr($urlPath, $prefixLength - 1);
    }

    protected static function getApplicationPath($urlPath) {
        if ($urlPath === '/') {
            return $urlPath;
        }
        $urlPath = preg_replace('#/{2,}#', '/', rtrim($urlPath, '/'));
        if ($urlPath === '') {
            return '/';
        }
        $extensionPosition = strrpos($urlPath, '.');
        if ($extensionPosition === false
            || $extensionPosition < strrpos($urlPath, '/'))
        {
            return $urlPath;
        }
        $_SERVER['REQUEST_MEDIA_TYPE'] = substr(
            $urlPath, $extensionPosition + 1
        );
        return substr($urlPath, 0, $extensionPosition);
    }
}
