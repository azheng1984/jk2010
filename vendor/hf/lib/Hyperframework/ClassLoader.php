<?php
namespace Hyperframework;

class ClassLoader {
    public static function run() {
        spl_autoload_register(array(__CLASS__, 'load'));
    }

    public static function load($name) {
        $name = str_replace('\\', '/', $name);
        if (static::startsWith($name, 'Hyperframework\Tool')) {
            require '/home/azheng/daoxila_www/vendor/hf/tool/lib/' . $name . '.php';
        }
        if (static::startsWith($name, 'Hyperframework')) {
            require '/home/azheng/daoxila_www/vendor/hf/lib/' . $name . '.php';
        }
   }

    private static function startsWith($haystack, $needle) {
        return $needle === "" || strpos($haystack, $needle) === 0;
    }
}
