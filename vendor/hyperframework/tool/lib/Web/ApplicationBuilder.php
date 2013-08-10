<?php
//namespace Hyperframework\Tool\Web;

class ApplicationBuilder {
    private $classLoader;

    public function build() {
        $config = require 'config' . DIRECTORY_SEPARATOR . 'application.config.php';
        $this->setUpClassLoader();
        $configuration = new ApplicationConfiguration;
        $handlers = $configuration->extract($config);
        $cache = new ApplicationCache($handlers);
        $directoryReader = new DirectoryReader(
            new ApplicationHandler($handlers, $cache)
        );
        $directoryReader->read($_SERVER['PWD'].DIRECTORY_SEPARATOR.'app');
        $this->tearDownClassLoader();
        return $cache;
    }

    protected function setUpClassLoader() {
        $rootPath = $_SERVER['PWD'].DIRECTORY_SEPARATOR;
        $cachePath = $rootPath 
            . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR;
        $cacheFile = $cachePath . 'class_loader.cache.php';
        if (!file_exists($cacheFile)) {
            throw new Exception("File '$cacheFile' does not exsit");
        }
        $this->classLoader = new Hyperframework\ClassLoader;
        $this->classLoader->run($rootPath, $cachePath);
    }

    protected function tearDownClassLoader() {
        if ($this->classLoader !== null) {
            $this->classLoader->stop();
        }
    }
}
