<?php
namespace Hyperframework\Web;

class ApplicationContext {
    private static $params;

    public static function get($name) {
        if (isset(self::$params[$name])) {
            return self::$params[$name];
        }
    }

    public function __set($name) {
        if (unset($ctx->id)) {
        }
        if (isset($ctx->id['id'])) {
        }
        $ctx->id = 23;
        $ctx->get('id');
        $ctx->has('id');
    }

    public static function 

    public static function export() {
        return self::$params;
    }

    public static function set($name, $value) {
        return self::$params[$name] = $value;
    }

    public static function has($name) {
        return isset(self::$params[$name]);
    }

    public static function remove($name) {
        unset(self::$params[$name]);
    }

    public static function reset() {
        self::$params = null;
    }
}
