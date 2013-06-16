<?php
namespace Hyperframework::Web;

class Application {
    private static $cache;
    private $config;
    private $isViewEnabled = true;

    public static function initialize($cache) {
        static::$cache = $cache;
    }
 
    public function run($path = null) {
        if ($path === null) {
            $segmentList = explode('?', $_SERVER['REQUEST_URI'], 2);
            $path = $segmentList[0];
        }
        if (static::$cache === null) {
            static::$cache = require CACHE_PATH . 'application.cache.php';
        }
        if (isset(static::$cache[$path]) === false) {
            throw new NotFoundException(
                'Application path \'' . $path . '\' not found'
            );
        }
        $this->config = static::$cache[$path];
        $this->dispatch();
    }
 
    public function redirect($location, $statusCode = '302 Found') {
        header('HTTP/1.1 ' . $statusCode);
        header('Location: ' . $location);
        $this->isViewEnabled = false;
    }

    protected function dispatch() {
        $this->executeAction();
        $this->executeView();
    }

    protected function executeAction() {
        $config = null;
        if (isset($this->config['Action'])) {
            $config = $this->config['Action'];
        }
        $processor = new ActionProcessor;
        $processor->run($config);
    }

    protected function executeView() {
        if ($this->isViewEnabled && isset($this->config['View'])) {
            $processor = new ViewProcessor;
            $processor->run($this->config['View']);
        }
    }
}
