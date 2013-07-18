<?php
class BuildCommand {
    public function execute() {
        $exporter = new CacheExporter;
        foreach ($this->getConfig() as $name => $config) {
            $exporter->export($this->dispatch($name, $config));
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

    private function dispatch($name, $config) {
        if (is_int($name)) {
            list($name, $config) = array($config, null);
        }
        try {
            $reflector = new ReflectionClass($name . 'Builder');
            $builder = $reflector->newInstance();
            return $builder->build($config);
        } catch (Exception $exception) {
            throw new CommandException($exception->getMessage());
        }
    }
}
