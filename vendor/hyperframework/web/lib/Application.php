<?php
namespace Hyperframework\Web;

class Application {
    private static $cache;
    private $isViewEnabled = true;
    private $actionResult;

    public static function initialize($cache) {
        static::$cache = $cache;
    }
 
    public function run($config) {
        $this->executeAction($config);
        $this->executeView($config);
    }
 
    public function redirect($location, $statusCode = '302 Found') {
        header('HTTP/1.1 ' . $statusCode);
        header('Location: ' . $location);
        $this->disableView();
    }

    public function getActionResult() {
        return $this->actionResult;
    }

    protected function executeAction($config) {
        $actionConfig = null;
        if (isset($config['Action'])) {
            $actionCinfig = $['Action'];
        }
        $processor = new ActionProcessor;
        $this->actionResult = $processor->run($actionConfig);
    }

    protected function executeView($config) {
        if ($this->isViewEnabled === false) {
            return;
        }
        if (isset($config['View']) === false) {
            throw new UnsupportedMediaTypeException;
        }
        $processor = new ViewProcessor;
        $processor->run($config['View']);
    }
}
