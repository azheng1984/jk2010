<?php
namespace Hyperframework\Web;

class Application {
    private static $instances = array();
    private $actionResult;
    private $isViewEnabled = true;

    public static function run($path = null, $name = 'default') {
        $pathInfo = PathInfo::get($path);
        $instance = static::createInstance($name);
        $instance->executeAction($pathInfo);
        $instance->renderView($pathInfo);
    }

    public static function get($name = 'default') {
        if (isset(static::$instances[$name]) === false) {
            throw new \Exception('Application \'' . $name . '\' not found');
        }
        return static::$instances[$name];
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

    protected static function createInstance($name) {
        if (isset(static::$instances[$name])) {
            throw new \Exception('Application \'' . $name . '\' existed');
        }
        $class = get_called_class();
        $instance = new $class($name);
        static::$instances[$name] = $instance;
        return $instance;
    }

    protected function __construct() {
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
}
