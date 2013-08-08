<?php
namespace Hyperframework\Web;

class Application {
    private static $info;
    private static $cacheDirectory;
    private $actionResult;
    private $isViewEnabled = true;

    public static function setCacheDirectory($value) {
        static::$cacheDirectory = $value;
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
            $cacheDirectory =
                static::$cacheDirectory === null ?
                    CACHE_PATH : static::$cacheDirecotry;
            static::$info = require $cacheDirectory . 'application.cache.php';
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
}
