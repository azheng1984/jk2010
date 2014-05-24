<?php
class ApplicationHandler {
    private $handlers;
    private $cache;

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
}
