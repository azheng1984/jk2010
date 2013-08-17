<?php
class ClassLoaderBuilder {
    private $classMappings = array();
    private $output = array();
    private $excludePaths = array();
    
    public function build() {
        $config = require 'config' . DIRECTORY_SEPARATOR . 'class_loader.config.php';
        $this->checkExcludePath('', $config);
        $this->processNamespace('', $config, array());
        $this->checkConflict(null, $this->output);
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
        var_export($this->output);
        //return array('class_loader' => $this->output);
    }

    private function checkExcludePath($namespace, &$current, $properties = array()) {
        if (is_string($current) && isset($properties['exclude']) && $properties['exclude'] === true) {
            $root = '';
            if (isset($properties['root'])) {
                $root = $properties['root'];
            }
            $target = &$this->excludePaths;
            $namespaces = explode('\\', $namespace);
            foreach ($namespaces as $item) {
                if ($item === '') {
                    continue;
                }
                if (isset($target[$item]) === false) {
                   $target[$item] = array(); 
                }
                $target = &$target[$item];
            }
            $target[] = $root . $current;
            return;
        }
        foreach ($current as $key => $value) {
            if (is_string[$key] && $key === '@exclude') {
                $properties['exclude'] = $value;
            }
            if (is_string[$key] && $key === '@root') {
                $properties['root'] = $value; //只支持绝对路径的 root
            }
        }
        $isExclude = false;
        if (isset($properties['exclude']) && $properties['exclude'] === true) {
            $isExclude = true;
        }
        foreach ($current as $key => $value) {
            if (is_int($key)) {
                if ($isExclude && is_string($value)) {
                    $this->checkExcludePath($namespace, $value, $properties);
               } elseif (is_array($value)) {
                    $this->checkExcludePath($namespace, &$current[$key], $properties);
                }
            } else {
                if (strncmp($key, '@', 1) === 0) {
                    continue;
                }
                $this->checkExcludePath($namespace . '\\' . $key, $current[$key], $properties);
            }
        }
    }

    private function checkConflict($parentNamespace, &$current) {
        if (is_string($current)) {
            return;
        }
        $folders = array();
        $namespaces = array();
//        var_dump($parentNamespace);
//        var_dump($current);
        foreach ($current as $key => $value) {
            if (is_int($key)) {
                $folders[$key] = $value;
            } else {
                if (strncmp($key, '@', 1) === 0) {
                    continue;
                }
                $namespaces[] = $key;
            }
        }
        $excludePaths = $this->getExcludePaths($namespace, $folder, true);
        foreach ($folders as $folder) {
            foreach ($namespaces as $namespace) {
                if (in_array($folder . '/' . $namespace, $excludePaths)) {//如果目录被 exclude，则不加
                    continue;
                }
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
        if (count($folders) > 1) {
//            var_dump($folders);
            foreach ($folders as $index => $folder) {
                if (is_dir($folder)) {
                    $d = dir($folder);
                    while (false !== ($ns = $d->read())) {
                        $childFolder = $folder . '/' . $ns;
                        //echo '>' . $childFolder . '<' . PHP_EOL;
                        if ($ns === '.' || $ns === '..') {
                            continue;
                        }
                        if (in_array($childFolder, $excludePaths)) {//如果目录被 exclude，则不加
                            continue;
                        }
                        if (is_dir($childFolder) === false) {//添加到文件映射
                            $this->addClassMapping($parentNamespace, $childFolder);
                            continue;
                        }
                        if (isset($current[$ns])) {
                            if (is_string($current[$ns])) {
                                $current[$ns] = array($childFolder, $current[$ns]);
                            } else {
                                $current[$ns][] = $childFolder;
                            }
                        } else {
                            $current[$ns] = $childFolder;
                        }
                        if (in_array($ns, $namespaces) === false) {
                            $namespaces[] = $ns;
                        }
                    }
                } else {
                    $this->addClassMapping($parentNamespace, $folder);
                }
                unset($current[$index]);
            }
        }
        foreach ($namespaces as $namespace) {
            if ($parentNamespace !== null) {
                $parentNamespace = $parentNamespace . '\\' . $namespace;
            } else {
                $parentNamespace = $namespace;
            }
            $this->checkConflict($parentNamespace, $current[$namespace]);
        }
        $tmp = &$current;
        foreach ($excludePaths as $path) {
            //add empty namespace path
            $subnamespaces = explode('/', substr($path, strlen($folder)));
            foreach ($subnamespaces as $item) {
                if (isset($tmp[$item] === false)) {
                    $tmp[$item] = array();
                }
                $tmp = &$tmp[$item];
            }
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
                //todo add properties here
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
        if (isset($properties['@folder_mapping']) &&
            $properties['@folder_mapping'] === false) {
            $folderMapping = false;
        } else {
           unset($properties['@folder_mapping']);
        }
        if (strncmp($folder, '/', 1) !== 0) {
            $folder = $_SERVER['PWD'] . '/' . $folder;
        }
        if (isset($properties['@folder_mapping'])) {
            if (isset($properties['@exclude']) === false) {
                $this->addClassMapping($namespace, $folder);
                return;
            }
        }
        if (isset($properties['@exclude']) &&
            $properties['@exclude'] === true) {
                return;
        } else {
            unset($properties['@exclude']);
        }
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

    private function addClassMapping($namespace, $folder) {
        //todo check exclude
        if (isset($this->classMappings[$namespace]) === false) {
            $cache = new ClassLoaderCache;
            $directoryReader = new DirectoryReader(
                new ClassRecognizationHandler($cache)
            );
            $this->classMappings[$namespace] = array(
                $directoryReader, $cache
            );
        }
        $excludePaths = $this->getExcludePaths($namespace, $folder, false);
        $this->classMappings[$namespace][0]->read($folder, null, $excludePaths);
    }

    private function getExcludePaths($namespace, $folder, $isFolderMapping) {
        $namespaces = explode('\\', $namespace);
        $result = array();
        $tmp = &$this->excludePaths;
        $currentNamespace = '';
        $checkChildNamespace = true;
        foreach ($namespaces as $item) {
            if ($item === '') {
                continue;
            }
            if (isset($tmp[$item])) {
                foreach ($tmp[$item] as $key => $value) {
                    if (is_int($key)) {
                        if (strncmp($folder, $value, strlen($folder)) === 0) {
                            $result[] = $value;
                        }
                    }
                }
            } else {
                $checkChildNamespace = false;
                break;
            }
            $tmp = &$tmp[$item];
            if ($currentNamespace === '') {
                $currentNamespace = $item;
            } else {
                $currentNamespace .= '\\' . $item;
            }
        }
        if ($checkChildNamespace && $isFolderMapping && is_array($tmp)) {
            //检查所有下层 namespace 是否有排除的子文件夹
            foreach ($tmp as $key => $value) {
                if (is_string($tmp($key))) {
                    $this->comparePathMapping($currentNamespace, $folder, $currentNamespace . '\\'. $key, $key[$tmp], $result);
                }
            }
        }
        return $result;
    }

    private function comparePathMapping($baseNamespace, $baseFolder, $currentNamespace, $current, &$result) {
        foreach ($current as $key => $value) {
            if (is_string($tmp($key))) {
                $this->comparePathMapping($baseNamespace, $baseFolder, $currentNamespace . '\\'. $key, $tmp[$key], $result);
            } else {
                $path = $tmp[$key];
                if (strncmp($path, $baseFolder, strlen($baseFolder)) === 0) {
                    if (substr($currentNamespace, strlen($baseNamespace)) ===
                        str_replace('/', '\\', substr($path, strlen($baseFolder)))) {
                        $result[] = $path;
                    }
                }
            }
        }
    }

    public function processProperties($config, $properties) {
        foreach ($config as $key => $value) {
            if (is_int($key) === false && strncmp($key, '@', 1) === 0) {
                //@root (可被覆盖或 '相对 root')
                //@folder_mapping
                //@exclude
                $properties[$key] = $value;
             }
        }
        return $properties;
    }
}
