<?php
namespace Hyperframework\Web;

use Hyperframework\ConfigLoader;

class PathInfoBuilder {
    private $defaultView;
    private $handlers;
    private $cache;
    private $classLoader;

    public function build($path, $options = null) {
        if (isset($options['default_view']) === false) {
            self::$defaultView = $options['default_view'];
        } else {
            self::$defaultView = array('Html', 'Xml', 'Json');
        }
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
        $rootPath = $_SERVER['PWD'] . DIRECTORY_SEPARATOR;
        $configPath = $rootPath . 'config' . DIRECTORY_SEPARATOR . 'class_loader.php';
        $config = ConfigLoader::load($configPath, array(''));
        if ($config === null) {
            throw new Exception("File '$cachePath' does not exsit");
        }
        if (!file_exists($cachePath)) {
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

    public static function build($name) {
        $baseNamespace = Hyperframework\APPLICATION_PATH . DIRECTORY_SEPARATOR
            . 'lib' . DIRECTORY_SEPARATOR . $name;
        $basePath = Hyperframework\APPLICATION_NAMESPACE . $name;
        $handler = readdir($basePath);
        $action = '';
    }
}
