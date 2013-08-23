<?php
namespace Hyperframework\Web;

class Application {
    private static $cacheProvider;
    private static $cache;
    private $actionResult;
    private $isViewEnabled = true;

    public function initialize($cacheProvider = null) {
        static::$cacheProvider = $cacheProvider;
        static::$cache = null;
    }

    public function run($path = null) {
        $info = $this->getPathInfo($path);
        $this->executeAction($info);
        $this->renderView($info);
    }

    public function enableView() {
        $this->isViewEnabled = true;
    }

    public function disableView() {
        $this->isViewEnabled = false;
    }

    public function getActionResult() {
        return $this->actionResult;
    }

    protected function getPathInfo($path) {
        if ($path === null) {
            $segments = explode('?', $_SERVER['REQUEST_URI'], 2);
            $path = $segments[0];
        }
        if (static::$cache === null) {
            $this->initializeCache();
        }
        if (isset(static::$cache['paths'][$path]) === false) {
            throw new NotFoundException('Path \'' . $path . '\' not found');
        }
        $info = static::$cache['paths'][$path];
        $info['namespace'] = $this->getNamespace($path);
        return $info;
    }

    protected function executeAction(
        $pathInfo, $processorClass = 'Hyperframework\Web\ActionProcessor'
    ) {
        $info = null;
        if (isset($pathInfo['Action'])) {
            $info = $pathInfo['Action'];
            $info['namespace'] = $pathInfo['namespace'];
        }
        $processor = new $processorClass;
        $this->actionResult = $processor->run($info);
    }

    protected function renderView(
        $pathInfo, $processorClass = 'Hyperframework\Web\ViewProcessor'
    ) {
        if (isset($pathInfo['View']) && $this->isViewEnabled) {
            $info = $pathInfo['View'];
            $info['namespace'] = $pathInfo['namespace'];
            $processor = new $processorClass;
            $processor->run($info);
        }
    }

    private function initializeCache() {
        if (is_array(static::$cacheProvider)) {
            $provider = new static::$cacheProvider[0]();
            static::$cache = $provider->{static::$cacheProvider[1]}();
            return;
        }
        $path = static::$cacheProvider === null ?
            CACHE_PATH . 'application.cache.php' : static::$cacheProvder;
        static::$cache = require $path;
    }

    private function getNamespace($path) {
        if (isset(static::$cache['namespace']) === false) {
            return '\\';
        }
        $namespace = static::$cache['namespace'];
        if (is_array($namespace) === false) {
            return '\\' . $namespace. '\\';
        }
        if (isset($namespace['folder_mapping']) === false) {
            throw new \Exception('Application cache format is incorrect');
        }
        $root = isset($namespace['root']) ? $namespace['root'] : null;
        if ($path === '/') {
            return $root === null ? '\\' : '\\' . $root. '\\';
        }
        $root = $root === null ? '' : '\\' . $root;
        return $root . str_replace('/', '\\', $path) . '\\';
    }
}
