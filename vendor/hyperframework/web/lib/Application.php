<?php
namespace Hyperframework\Web;

class Application {
    private static $instances = array();
    private $actionResult;
    private $isViewEnabled = true;

    public static function run($path = null, $name = 'main') {
        $info = PathInfo::get($path);
        $class = get_called_class();
        $app = new $class($name);
        $app->executeAction($info);
        $app->renderView($info);
    }

    public static function get($name = 'main') {
        return static::$instances[$name];
    }

    protected function __construct($name) {
        static::$instances[$name] = $this;
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
