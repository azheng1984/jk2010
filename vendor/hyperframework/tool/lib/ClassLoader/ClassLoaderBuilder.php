<?php
class ClassLoaderBuilder {
    private $classMappings = array();

    public function build() {
        $config = require 'config' . DIRECTORY_SEPARATOR . 'class_loader.config.php';
        $this->processNamespace('', $config, array());
        foreach ($this->classMappings as $namespace => $classMapping) {
            $tmp = $classMapping[1]->export();
            $namespaces = explode('\\', $namespace);
            $target = &$this->output;
            foreach ($namespaces as $item) {
                if ($item === '') {
                    continue;
                }
                if (isset($target[$item]) === false) {
                    if (is_string($target)) {
                        $target = array($target);
                    } else {
                        $target[$item] = array();
                    }
                }
                $target = &$target[$item];
            }
            if (is_string($target)) {
                $target = array($target);
            }
            $target['@classes'] = $tmp['class_loader'];
        }
        $this->checkConflict($this->output);
        $cache = var_export($this->output, true);
        echo $cache;
        exit;
        return $cache;
    }

    private function checkConflict(&$current) {
        if (is_string($current)) {
            return;
        }
        $folders = array();
        $namespaces = array();
        foreach ($current as $key => $value) {
            if (is_int($key)) {
                $folders[] = $value;
            } else {
                if (strncmp($key, '@', 1) === 0) {
                    continue;
                }
                $namespaces[] = $key;
            }
        }
        foreach ($folders as $folder) {
            foreach ($namespaces as $namespace) {
                if (is_dir($folder . '/' . $namespace)) {
                    if (is_string($current[$namespace])) {
                        $current[$namespace] = array(
                            $current[$namespace], $folder . '/' . $namespace
                        );
                    } else {
                        $current[$namespace][] = $folder . '/' . $namespace;
                    }
                }
            }
        }
        if (count($folders) < 2) {
            foreach ($folders as $folder) {
                if (is_dir($folder) === false) {
                    
                }
            }
        }
        foreach ($namespaces as $namespace) {
            $this->checkConflict($current[$namespace]);
        }
    }

    public function processNamespace($namespace, $config, $properties) {
        if (is_string($config)) {
            $this->processFolder($namespace, $config, $properties);
            return;
        }
        $properties = $this->processProperties($config, $properties);
        foreach ($config as $key => $value) {
            if (is_int($key)) {
                $this->processFolder($namespace, $value, $properties);
            } elseif (strncmp($key, '@', 1) !== 0) {
                if ($namespace !== '') {
                    $namespace .= '\\';
                }
                $this->processNamespace(
                    $namespace . $key, $value, $properties
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
            if (strncmp($key, '@', 1) === 0) {
                continue;
            }
            if (is_int($key)) {
                if (is_array($value)) {
                    $this->processFolder($namespace, $value, $properties);
                } else {
                    $this->addMapping($namespace, $value, $properties);
                }
            }
        }
    }

    public function addMapping($namespace, $folder, $properties) {
        if (isset($properties['@root'])) {
            if ($folder === '.') {
                $folder = $properties['@root'];
            } else {
                $folder = $properties['@root'] . '/'  . $folder;
            }
            unset($properties['@root']);
        }
        $recursive = true;
        $folderMapping = true;
        $exclude = false;
        if (isset($properties['@recursive']) &&
            $properties['@recursive'] === false) {
            $recursive = false;
        } else {
            unset($properties['@recursive']);
            //echo '[recursive]';
        }
        if (isset($properties['@folder_mapping']) &&
            $properties['@folder_mapping'] === false) {
            $folderMapping = false;
        } else {
           unset($properties['@folder_mapping']);
           // echo '[folder_mapping]';
        }
        if (strncmp($folder, '/', 1) !== 0) {
            $folder = $_SERVER['PWD'] . '/' . $folder;
        }
        if (isset($properties['@exclude']) &&
            $properties['@exclude'] === true) {
            $exclude = true;
            //echo '[exclude]';
        } else {
            unset($properties['@exclude']);
        }
       // if (count($properties) > 0) {
       //     $folder = array($folder) +  $properties;
       // }
        if (isset($properties['@folder_mapping'])) {
            if (isset($properties['@exclude'])) {
                return;
            }
            if (isset($this->classMappings[$namespace]) === false) {
                $cache = new ClassLoaderCache;
                $directoryReader = new DirectoryReader(
                    new ClassRecognizationHandler($cache)
                );
                $this->classMappings[$namespace] = array(
                    $directoryReader, $cache
                );
            }
            $this->classMappings[$namespace][0]->read($folder);
            return;
        }
        //todo 自动扩展一个命名空间中的文件夹 & 上层自动匹配的文件夹检查是否有匹配的子 namespace

        $currentNamespace = &$this->output;
        $count = 0;
        $namespaces = explode('\\', $namespace);
        //print_r($namespace);
        $amount = count($namespaces);
        //echo $amount;
        //echo $namespace . ' ' . $folder . PHP_EOL;
        //var_export($this->output);
        foreach ($namespaces as $item) {
            ++$count;
            //echo $count . ' '. $item;
            if ($item === '') {
                continue;
            }
            if ($count === $amount) {
                if (isset($currentNamespace[$item])) {
                    if (is_string($currentNamespace[$item])) {
                        $currentNamespace[$item] = array($currentNamespace[$item], $folder);
                    } else {
                        $currentNamespace[$item][] = $folder; 
                    }
                } else {
                    if (is_string($currentNamespace)) {
                        $currentNamespace = array($currentNamespace, $item => $folder);
                    } else {
                        $currentNamespace[$item] = $folder;
                    }
                }
            } elseif (isset($currentNamespace[$item]) === false) {
                if (is_string($currentNamespace)) {
                    $currentNamespace = array($currentNamespace, $item => array());
                } else {
                    $currentNamespace[$item] = array();
                }
            }
            $currentNamespace = &$currentNamespace[$item];
        }
        //var_export($this->output);
    }

    private $output = array();

    public function processProperties($config, $properties) {
        foreach ($config as $key => $value) {
            if (is_int($key) === false && strncmp($key, '@', 1) === 0) {
                //@root (可被覆盖或 '相对 root')
                //@folder_mapping
                //@recursive
                //@exclude
                $properties[$key] = $value;
             }
        }
        return $properties;
    }
}
