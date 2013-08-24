<?php
namespace Hyperframework\Web;

class Application {
    private $actionResult;
    private $isViewEnabled = true;
    private static $instances = array();

    public static function run(
        $path = null, $name = 'main', $class = __CLASS__
    ) {
        $app = new $class;
        static::$instance[$name] = $app;
        $info = PathInfo::get($path);
        $app->executeAction($info);
        $app->renderView($info);
    }

    public static function get($name = 'main') {
        return satic::$instance[$name];
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
