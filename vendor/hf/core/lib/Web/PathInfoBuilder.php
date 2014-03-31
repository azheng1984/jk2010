<?php
namespace Hyperframework\Web;

class PathInfoBuilder {
    private $handlers;
    private $cache;
    private $classLoader;

    public function build($path) {
        //default config: array(
        //    'Action' => 'Hyperframework\Web\ActionInfoBuilder'
        //    array('View', 'Html', 'Desktop', 'Phone') => 'Hyperframework\Web\ViewInfoBuilder'
        //)
        //1. tool 需要 merge app 的 class loader 可能需要 extends 类
        //2. 需要链式派发，还是精确定位？
        $config = require 'config' . DIRECTORY_SEPARATOR . 'application.php';
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
        $cachePath = $rootPath . 'cache' . DIRECTORY_SEPARATOR . 'class_loader.cache.php';
        if (!file_exists($cachePath)) {
            throw new Exception("File '$cachePath' does not exsit");
        }
        require HYPERFRAMEWORK_PATH . 'Hyperframework' .
            DIRECTORY_SEPARATOR . 'ClassLoader.php';
        $this->classLoader = new Hyperframework\ClassLoader;
        $this->classLoader->run($rootPath, $cachePath);
    }

    protected function tearDownClassLoader() {
        if ($this->classLoader !== null) {
            $this->classLoader->stop();
        }
    }

    public function __construct($handlers, $cache) {
        $this->handlers = $handlers;
        $this->cache = $cache;
    }

    public function handle($fullPath, $relativeFolder) {
        $classRecognizer = new ClassRecognizer;
        $class = $classRecognizer->getClass(basename($fullPath));
        if ($class === null) {
            return;
        }
        foreach ($this->handlers as $name => $handler) {
            $cache = $handler->handle($class, $fullPath);
            if ($cache !== null) {
                $this->cache->append($relativeFolder, $name, $cache);
                return;
            }
        }
    }

    public static function build($path) {
        $dir = Hyperframework\APPLICATION_PATH . DIRECTORY_SEPARATOR . 'lib'
            . DIRECTORY_SEPARATOR . 'App' . DIRECTORY_SEPARATOR  . $path;

    }

    private static function getNamespace($path) {
        explode('/', $path);
    }

    private static function getPath($path) {
    }
}
