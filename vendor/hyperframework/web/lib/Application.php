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
            $segments = explode('?', $_SERVER['REQUEST_URI'], 2);
            $path = $segments[0];
        }
        if ($this->cache === null) {
            $this->initializeCache();
        }
        if (isset($this->cache[$path]) === false) {
            throw new NotFoundException(
                'Path \'' . $path . '\' not found'
            );
        }
        return $this->cache[$path];
    }

    protected function executeAction(
        $info, $processorClass = 'Hyperframework\Web\ActionProcessor'
    ) {
        $actionInfo = null;
        if (isset($info['Action'])) {
            $actionInfo = $info['Action'];
        }
        $processor = new $processorClass;
        $this->actionResult = $processor->run($actionInfo);
    }

    protected function renderView(
        $info, $processorClass = 'Hyperframework\Web\ViewProcessor'
    ) {
        if (isset($info['View']) && $this->isViewEnabled) {
            $processor = new $processorClass;
            $processor->run($info['View']);
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
}
