<?php
namespace Hyperframework\Web;

class Application {
    private static $info;
    private static $cacheDirectory;
    private static $cacheProvider;
    private $actionResult;
    private $isViewEnabled = true;

    public static function setCacheDirectory($value) {
        static::$cacheDirectory = $value;
    }

    public static function setCacheProvider($value) {
        static::$cacheProvider = $value;
    }

    public static function reset() {
        static::$info = null;
        static::$cacheDirectory = null;
        static::$cacheProvider = null;
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
        if (static::$info === null) {
            $this->initializeInfo();
        }
        if (isset(static::$info[$path]) === false) {
            throw new NotFoundException(
                'Path \'' . $path . '\' not found'
            );
        }
        return static::$info[$path];
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

    private function initializeInfo() {
        $cachePath = (static::$cacheDirectory === null ?
            CACHE_PATH : static::$cacheDirecotry) . 'application.cache.php';
        if (static::$cacheProvider === null) {
            static::$info = require $cachePath;
            return;
        }
        static::$info = static::$cacheProvider->get($cachePath);
    }
}
