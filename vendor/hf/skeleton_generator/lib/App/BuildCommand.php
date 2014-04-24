<?php
namespace Hyperframework\Tool\App;

class BuildCommand {
    public function execute() {
        $exporter = new CacheExporter;
        foreach ($this->getConfig() as $name) {
            $exporter->export($this->dispatch($name));
        }
    }

    private function getConfig() {
        $path = 'config' . DIRECTORY_SEPARATOR . 'build.config.php';
        if (file_exists($path) === false) {
            throw new CommandException("Can't find the '$path'");
        }
        $config = require $path;
        if (is_array($config) === false) {
            $config = array($config);
        }
        return $config;
    }

    private function dispatch($name) {
        if (is_int($name)) {
            list($name, $config) = array($config, null);
        }
        try {
            $reflector = new ReflectionClass($name . 'Builder');
            $builder = $reflector->newInstance();
            return $builder->build();
        } catch (Exception $exception) {
            throw new CommandException($exception->getMessage());
        }
    }
}
