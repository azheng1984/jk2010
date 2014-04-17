<?php
namespace Hyperframework\Web;

use Hyperframework\ConfigLoader;

class PathInfoBuilder {
    private $defaultView;
    private $handlers;
    private $cache;
    private $classLoader;

    public function build($path, $namespace, $options = null) {
        if (isset($options['default_view']) === false) {
            self::$defaultView = $options['default_view'];
        } else {
            self::$defaultView = array('Html', 'Xml', 'Json');
        }
        foreach(scandir($path) as $fileName) {
            
        }
        $configuration = new ApplicationConfiguration;
        $handlers = $configuration->extract($config);
        $cache = new ApplicationCache($handlers);
        $directoryReader = new DirectoryReader(
            new ApplicationHandler($handlers, $cache)
        );
        $directoryReader->read($_SERVER['PWD'] . DIRECTORY_SEPARATOR .'app');
        return $cache;
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
