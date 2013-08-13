<?php
class ClassLoaderBuilder {
    private $cache;

    public function build() {
        $config = require 'config' . DIRECTORY_SEPARATOR . 'class_loader.config.php';
        $this->processNamespace('', $config, array());
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
        }
        if (isset($properties['@recursive']) &&
            $properties['@recursive'] === false) {
        } else {
            echo '[recursive]';
        }
        if (isset($properties['@folder_mapping']) &&
            $properties['@folder_mapping'] === false) {
           //todo 扫描所有子文件，找出类
        } else {
            echo '[folder_mapping]';
        }
        if (strncmp($folder, '/', 1) !== 0) {
            $folder = $_SERVER['PWD'] . '/' . $folder;
        }
        if (isset($properties['@exclude']) &&
            $properties['@exclude'] === true) {
            echo '[exclude]';
        }
        //todo
        // 1: 在 folder_mapping = true 下 exclude，会显式阻止匹配，folder_mapping false 时，扫描器不会进入
        // 2: 一个命名空间中同时对应两个文件夹，则需要扫描下层文件，如果下层文件还是冲突，扫描下一层，直到所有路径都是明确的
        // 3: 压缩路径(使用尽量少的配置完成文件定位)：在 folder_mapping 的情况下，在没有歧义时进行，比如 \X\Y\Z.php 对应 /x/Y/Z.php 路径，其实只要 X => x 足够了
        // 4: recursive = false 停止所有 children 的扫描，如果是 folder_mapping = true 时，在输出 cache 中设置中断匹配标记
        // 5: 配置允许 “叠加”, 除非是属性配置 比如 recursive = false/true
        // 6: path 反向匹配命名空间（用于 app build）
        echo $namespace . ' > ' . $folder . PHP_EOL;
    }

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
