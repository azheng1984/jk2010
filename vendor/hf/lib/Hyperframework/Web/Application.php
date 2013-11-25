<?php
namespace Hyperframework\Web;

class Application {
    private static $instance;
    private $actionResult;
    private $isViewEnabled = true;

    public static function run($path = null) {
        $pathInfo = PathInfo::get($path);
        $instance = static::create();
        $instance->executeAction($pathInfo);
        $instance->renderView($pathInfo);
    }

    public static function get() {
        $result = end(static::$instance);
        if ($result === false) {
            throw new \Exception('No application');
        }
        return $result;
    }

    public static function reset() {
        static::$instance = null;
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

    protected static function create() {
        if (static::$instance !== null) {
            throw new \Exception('Application already exists');
        }
        $class = get_called_class();
        $instance = new $class($name);
        static::$instances = $instance;
        return $instance;
    }

    protected function __construct() {}

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
            if (is_string($info)) {
               $info = ['Screen' => $info];
            }
            $info['namespace'] = $pathInfo['namespace'];
            $processor = new $processorClass;
            $processor->run($info);
        }
    }
}
