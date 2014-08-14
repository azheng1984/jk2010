<?php
namespace Hyperframework;

class ClassCacheBuilder {
    private static $classMap = array();
    private static $cacheRootPath;
    private static $isZeroFolderEnabled;

    public static function run() {
        if (Config::get('hyperframework.class_loader.enable_zero_folder')
            === true
        ) {
            self::$isZeroFolderEnabled = true;
        } else {
            self::$isZeroFolderEnabled = false;
        }
        self::$cacheRootPath = Config::get(
            'hyperframework.class_loader.cache_root_path'
        );
        if (self::$cacheRootPath === null) {
            self::$cacheRootPath = APP_ROOT_PATH . DIRECTORY_SEPARATOR
                . 'tmp' . DIRECTORY_SEPARATOR . 'cache'
                . DIRECTORY_SEPARATOR . 'lib';
        }
        $folder = \Hyperframework\APP_ROOT_PATH . DIRECTORY_SEPARATOR
            . 'vendor' . DIRECTORY_SEPARATOR . 'composer';
        self::$classMap = require(
            $folder . DIRECTORY_SEPARATOR . 'autoload_classmap.php'
        );
        foreach (self::$classMap as $key => &$value) {
            $value = realpath($value);
        }
        $psr0Config = require(
            $folder . DIRECTORY_SEPARATOR . 'autoload_namespaces.php'
        );
        $psr4Config = require(
            $folder . DIRECTORY_SEPARATOR . 'autoload_psr4.php'
        );
        self::processPsr4Config($psr4Config);
        self::processPsr0Config($psr0Config);
        self::generateCache();
        $phar = new \Phar(self::$cacheRootPath . '/../lib.phar');
        $phar->buildFromDirectory('/home/az/quickquick/vendor/backup');
        $phar->buildFromDirectory('/home/az/quickquick/vendor/packages');
        $phar->buildFromDirectory('/home/az/vim74');
        $phar->buildFromDirectory('/home/az/goagent-3.0');
        $phar->buildFromDirectory('/home/az/Downloads/wwwroot');
        $phar->buildFromDirectory('/home/az/Downloads/zf2-master');
        $phar->buildFromDirectory(self::$cacheRootPath);
    }

    private static function generateCache() {
        if (is_dir(self::$cacheRootPath)) {
            self::clearCache(self::$cacheRootPath, true);
        } else {
            mkdir(self::$cacheRootPath);
        }
        foreach (self::$classMap as $key => $value) {
            self::copyFile($key, $value);
        }
    }

    private static function generateAutoloadFiles() {
    }

    private static function clearCache($folder, $keepDir = false) {
        foreach (scandir($folder) as $entry) {
            if ($entry === '..' || $entry === '.') {
                continue;
            }
            $path = $folder . '/' . $entry;
            if (is_file($path)) {
                if (self::isClassFile($path)) {
                    unlink($path);
                }
            } else {
                self::clearCache($path);
            }
        }
        if ($keepDir === false && count(scandir($folder)) === 2) {
            rmdir($folder);
        }
    }

    private static function copyFile($class, $sourcePath) {
        if ($sourcePath === null) {
            return;
        }
        $segments = null;
        if (self::$isZeroFolderEnabled && strpos($class, '\\') === false) {
           $segments = explode('_', $class);
           array_unshift($segments, '0');
        } else {
            $segments = explode('\\', $class);
        }
        $count = count($segments);
        $path = self::$cacheRootPath;
        for ($index = 0; $index < $count - 1; ++$index) {
            $path .= DIRECTORY_SEPARATOR . $segments[$index];
            if (is_dir($path) === false) {
                mkdir($path);
            }
        }
        $path .= DIRECTORY_SEPARATOR . $segments[$count - 1] . '.php';
        copy($sourcePath, $path);
    }

    private static function processPsr0Config($config) {
        foreach ($config as $key => $paths) {
            foreach ($paths as $path) {
                $path = realpath($path);
                if ($path === null) {
                    continue;
                }
                if ($key === '') {
                    self::generatePsr0ClassMap($key, $path);
                    continue;
                }
                $relativePath = str_replace('\\', DIRECTORY_SEPARATOR, $key);
                $folder1 = $path;
                if (substr($path, -1) !== DIRECTORY_SEPARATOR) {
                    $folder1 .= DIRECTORY_SEPARATOR;
                }
                $file = $folder2 = $folder1;
                $folder1 .= $relativePath;
                if (is_dir($folder1)) {
                    self::generatePsr0ClassMap($key, $path, $relativePath);
                }
                $tmp = explode('\\', $key);
                array_push(
                    $tmp, str_replace('_', DIRECTORY_SEPARATOR, array_pop($tmp))
                );
                $relativePath = implode(DIRECTORY_SEPARATOR, $tmp);
                $folder2 .= $relativePath;
                if ($folder2 !== $folder1 && is_dir($folder2)) {
                    self::generatePsr0ClassMap($key, $path, $relativePath);
                }
                $lastChar = substr($key, -1);
                if ($lastChar !== '_' && $lastChar !== '\\') {
                    $relativePath .= '.php';
                    $file .= $relativePath;
                    if (is_file($file)) {
                        self::generatePsr0ClassMap($key, $path, $relativePath);
                    }
                }
            }
        }
    }

    private static function generatePsr0ClassMap(
        $classPrefix, $basePath, $relativePath = null
    ) {
        $path = $basePath;
        if (substr($path, -1) !== DIRECTORY_SEPARATOR) {
            $path .= DIRECTORY_SEPARATOR;
        }
        $path .= $relativePath;
        if (is_file($path)) {
            if (self::isClassFile($path) === false) {
                return;
            }
            $classes = ClassFileHelper::getClasses($path);
            foreach ($classes as $class) {
                if (strncmp($classPrefix, $class, strlen($classPrefix)) !== 0) {
                    continue;
                }
                $tmp = explode('\\', $class);
                $className = array_pop($tmp);
                array_push(
                    $tmp, str_replace('_', DIRECTORY_SEPARATOR, $className)
                );
                $tmp = implode(DIRECTORY_SEPARATOR, $tmp) . '.php';
                if ($tmp !== $relativePath) {
                    continue;
                }
                if (isset(self::$classMap[$class]) === false) {
                    self::$classMap[$class] = $path;
                }
            }
            return;
        }
        if ($relativePath !== null
            && self::isNamespace(basename($path)) === false
        ) {
            return;
        }
        foreach (scandir($path) as $entry) {
            if ($entry === '..' || $entry === '.') {
                continue;
            }
            $tmp = $relativePath;
            if ($relativePath !== null
                && substr($relativePath, -1) !== DIRECTORY_SEPARATOR
            ) {
                $tmp .= DIRECTORY_SEPARATOR;
            }
            self::generatePsr0ClassMap($classPrefix, $basePath, $tmp . $entry);
        }
    }

    private static function processPsr4Config($config) {
        foreach ($config as $key => $paths) {
            foreach ($paths as $path) {
                $path = realpath($path);
                if ($path === null) {
                    continue;
                }
                self::generatePsr4ClassMap($key, $path);
            }
        }
    }

    private static function generatePsr4ClassMap(
        $namespace, $basePath, $relativePath = null
    ) {
        $path = $basePath;
        if (substr($path, -1) !== DIRECTORY_SEPARATOR) {
            $path .= DIRECTORY_SEPARATOR;
        }
        $path .= $relativePath;
        if (is_file($path)) {
            if (self::isClassFile($path)) {
                if (isset(self::$classMap[$namespace]) === false) {
                    self::$classMap[$namespace] = $path;
                }
            }
            return;
        }
        if ($relativePath !== null
            && self::isNamespace(basename($path)) === false
        ) {
            return;
        }
        if ($relativePath !== null
            && substr($relativePath, -1) !== DIRECTORY_SEPARATOR
        ) {
            $relativePath .= DIRECTORY_SEPARATOR;
        }
        if ($namespace !== '' && substr($namespace, -1) !== '\\') {
            $namespace .= '\\';
        }
        foreach (scandir($path) as $entry) {
            if ($entry === '..' || $entry === '.') {
                continue;
            }
            $nextNamespace = $namespace;
            if (is_file($path . '/' . $entry)) {
                $nextNamespace .= ClassFileHelper::getClassNameByFileName($entry);
            } else {
                $nextNamespace .= $entry;
            }
            self::generatePsr4ClassMap(
                $nextNamespace, $basePath, $relativePath . $entry
            );
        }
    }

    private static function isClassFile($path) {
        return ClassFileHelper::getClassNameByFileName(basename($path)) !== null;
    }

    private static function isNamespace($name) {
        $pattern = '/^([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)$/';
        return preg_match($pattern, $name) === 1;
    }
}
