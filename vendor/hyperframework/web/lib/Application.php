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
            static::$info = require CACHE_PATH . 'appication_info.cache.php';
        }
        if (isset(static::$cache[$path]) === false) {
            throw new NotFoundException(
                'Application path \'' . $path . '\' not found'
            );
        }
        return static::$cache[$path];
    }

    protected function executeAction($info) {
        $actionInfo = null;
        if (isset($info['Action'])) {
            $actionInfo = $info['Action'];
        }
        $processor = new ActionProcessor;
        $this->actionResult = $processor->run($actionInfo);
    }

    protected function renderView($info) {
        if (isset($info['View']) && $this->isViewEnabled) {
            $processor = new ViewProcessor;
            $processor->run($info['View']);
        }
    }
}
