<?php
namespace Hyperframework\Web;

class Application {
    private $actionResult;
    private $cachePath;
    private $cacheProvider;
    private $cache;
    private $isViewEnabled = true;

    public function __construct($cachePath = null, $cacheProvider = null) {
        $this->cachePath = $cachePath;
        $this->cacheProvider = $cacheProvider;
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
            $segments = \explode('?', $_SERVER['REQUEST_URI'], 2);
            $path = $segments[0];
        }
        if ($this->cache === null) {
            $this->initializeCache();
        }
        if (isset($this->cache['paths'][$path]) === false) {
            throw new NotFoundException('Path \'' . $path . '\' not found');
        }
        $info = $this->cache['paths'][$path];
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
        $path = $this->cachePath === null ?
            CACHE_PATH . 'application.cache.php' : $this->cachePath;
        if ($this->cacheProvider === null) {
            $this->cache = require $path;
            return;
        }
        $this->cache = $this->cacheProvider->get($path);
    }

    private function getNamespace($path) {
        if (isset($this->cache['namespace']) === false) {
            return '\\';
        }
        $cache = $this->cache['namespace'];
        if (\is_array($cache) === false) {
            return '\\' . $cache . '\\';
        }
        if (isset($cache['folder_mapping']) === false) {
            throw new \Exception('Application cache format is incorrect');
        }
        $namespace = isset($cache[0]) ? $cache[0] : null;
        if ($path === '/') {
            return $namespace === null ? '\\' : '\\' . $namespace . '\\';
        }
        $namespace = $namespace === null ? '' : '\\' . $namespace;
        return $namespace . \str_replace('/', '\\', $path) . '\\';
    }
}
