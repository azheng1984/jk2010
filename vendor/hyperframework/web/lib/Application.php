<?php
namespace Hyperframework\Web;

class Application {
    private static $cache;
    private $config;
    private $isViewProcessorEnabled = true;

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
        $this->disableViewProcessor();
    }

    public function enableViewProcessor() {
        $this->isViewProcessorEnabled = true;
    }

    public function disableViewProcessor() {
        $this->isViewProcessorEnabled = false;
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
        if ($this->isViewProcessorEnabled === false) {
            return;
        }
        if (isset($this->config['View']) === false) {
            throw new UnsupportedMediaTypeException;
        }
        $processor = new ViewProcessor;
        $processor->run($this->config['View']);
    }
}
