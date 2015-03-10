<?php
namespace Hyperframework\Web;

use Hyperframework\Common\Config;
use Hyperframework\Common\ClassNotFoundException;

class Request {
    private static $engine;

    public static function getHeaders() {
        return self::getEngine()->getHeaders();
    }

    public static function openInputStream() {
        return self::getEngine()->openInputStream();
    }

    public static function getEngine() {
        if (self::$engine === null) {
            $configName = 'hyperframework.web.request_engine_class';
            $class = Config::getString($configName , '');
            if ($class === '') {
                self::$engine = new RequestEngine;
            } else {
                if (class_exists($class) === false) {
                    throw new ClassNotFoundException(
                        "Request engine class '$class' "
                            . "does not exist, set using config '$configName'."
                    );
                    self::$engine = new $class;
                }
            }
        }
        return self::$engine;
    }

    public static function setEngine($value) {
        self::$engine = $value;
    }
}
