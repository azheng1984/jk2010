<?php
use Hyperframework\Config;

class Router {
    public function execute() {
        namespace ''
        Params::get('id');
        ApplicationContext::get('id');
        PathContext::get('id');
        Application::setParam('id');
        $id = $GLOBALS['app_context']['id'];
        $id = ActionResult::get('id');
        ApplicationParams::get('id');
        if (isset($_GET['@id'])) {
            throw Exception;
        }
        $_GET['#'] = array();
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

    public function getId(0) {
    }

    protected function getUrlPath() {
        $segments = explode('?', $_SERVER['REQUEST_URI'], 2);
        $result = $segments[0];
        if ($result === '') {
            return '/';
        }
        if ($result[0] === '#') {
            throw new NotFoundException;
        }
        $id1 = $_GET['#id-1'];
        use Hyperframework\Web\PathContext;

        return $result;
    }

    protected function getAssetPath($urlPath) {
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

    protected function getApplicationPath($urlPath) {
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
