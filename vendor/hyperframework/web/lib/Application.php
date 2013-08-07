<?php
namespace Hyperframework\Web;

class Application {
    private static $info;
    private $actionResult;
    private $cachePath;
    private $isViewEnabled = true;

    public function __construct($cachePath = CACHE_PATH) {
        $this->cachePath = $cachePath;
    }

    public function run($path = null) {
        $info = $this->getInfo($path);
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

    protected function getInfo($path) {
        if ($path === null) {
            $segments = explode('?', $_SERVER['REQUEST_URI'], 2);
            $path = $segments[0];
        }
        if (static::$info === null) {
            static::$info = require $this->cachePath . 'application.cache.php';
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
