<?php
namespace Hyperframework\Web;

class Application {
    private static $instances = array();
    private $actionResult;
    private $isViewEnabled = true;

    public static function run($path = null, $name = 'main') {
        $pathInfo = PathInfo::get($path);
        $application = static::create($name);
        $application->executeAction($pathInfo);
        $application->renderView($pathInfo);
    }

    public static function get($name = 'main') {
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

    protected static function create($name) {
        $class = get_called_class();
        return new $class($name);
    }

    protected function __construct($name) {
        static::$instances[$name] = $this;
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
