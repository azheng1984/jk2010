<?php
class ClassLoaderBuilder {
    private $cache;

    public function build() {
        $config = require 'config' . DIRECTORY_SEPARATOR . 'class_loader.config.php';
        $this->processNamespace('\\', $config);
        exit;
        $cache = new ClassLoaderCache;
        $directoryReader = new DirectoryReader(
            new ClassRecognizationHandler($cache)
        );
        $configuration = new ClassLoaderConfiguration;
        foreach ($configuration->extract($config) as $item) {
            $directoryReader->read($item[0], $item[1]);
        }
        return $cache;
    }

    public function processNamespace($namespace, $config, $properties) {
        foreach ($config as $key => $value) {
            if (is_int($key) === false && strpos($key, '@') === 0) {
                //@root
                //@folder_mapping
                //@recursive
                $properties[$key] = $value;
             }
        }
        foreach ($config as $key => $value) {
            if (is_int($key)) {
                $this->processFolder($namespace, $value, $properties);
            } elseif (strpos($key, '@') !== 0) {
                $this->processNamespace($namespace . $key, $value, $properties);
            }
        }
    }

    public function processFolder($namespace, $config, $properties) {
        foreach ($config as $key => $value) {
            if (is_int($key) === false && strpos($key, '@') === 0) {
                //@root
                //@folder_mapping
                //@recursive
                //@exclude
                $properties[$key] = $value;
             }
        }
        foreach ($config as $key => $value) {
            if (is_int($key)) {
                if (is_array($value)) {
                    $this->processFolder($namespace, $value, $properties);
                }
            }
        }
    }

    public function addCache($namespace, $folder) {

    }
}
