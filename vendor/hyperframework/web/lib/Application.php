<?php
namespace Hyperframework\Web;

class Application {
    private static $info;
    private $isViewEnabled = true;
    private $actionResult;

    public static function initialize($info) {
        static::$info = $info;
    }

    public function run($path = null) {
        $info = $this->getInfo($path);
        $this->executeAction($info);
        throw new \Exception;
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
            static::$info = require CACHE_PATH . 'application.cache.php';
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
