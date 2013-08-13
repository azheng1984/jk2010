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
        if (is_string($config)) {
            $this->processFolder($namespace, $config, $properties);
            return;
        }
        $properties = $this->processFolder($config, $properties);
        foreach ($config as $key => $value) {
            if (is_int($key)) {
                $this->processFolder($namespace, $value, $properties);
            } elseif (strncmp($key, '@', 1) !== 0) {
                $this->processNamespace(
                    $namespace . '\\' . $key, $value, $properties
                );
            }
        }
    }

    public function processFolder($namespace, $config, $properties) {
        if (is_string($config)) {
            $this->addMapping($namespace, $config, $properties);
            return;
        }
        $properties = $this->processProperties($config, $properties);
        foreach ($config as $key => $value) {
            if (is_int($key)) {
                if (is_array($value)) {
                    $this->processFolder($namespace, $value, $properties);
                }
            } else {
                $this->addMapping($namespace, $value, $properties);
            }
        }
    }

    public function addMapping($namespace, $folder, $properties) {
        if (isset($properties['@root'])) {
            $folder = $properties['@root'] . $folder;
        }
//        if (isset($properties['folder_mapping']) &&
//            $properties['folder_mapping'] === false) {
//
//            }
        echo $namespace . ' > ' . $folder . PHP_EOL;
    }

    public function processProperties($config, $properties) {
        foreach ($config as $key => $value) {
            if (is_int($key) === false && strncmp($key, '@', 1) === 0) {
                //@root
                //@folder_mapping
                //@recursive
                //@exclude
                $properties[$key] = $value;
             }
        }
        return $properties;
    }
}
