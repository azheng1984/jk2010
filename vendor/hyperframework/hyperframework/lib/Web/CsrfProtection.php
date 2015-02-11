<?php
namespace Hyperframework\Web;

use Hyperframework\Common\Config;
use Hyperframework\Common\ClassNotFoundException;

class CsrfProtection {
    private static $engine;

    public static function run() {
        if (static::isEnabled()) {
            $engine = static::getEngine();
            $engine->run();
        }
    }

    public static function isEnabled() {
        return Config::getBoolean(
            'hyperframework.web.csrf_protection.enable', true
        );
    }

    public static function getToken() {
        $engine = static::getEngine();
        return $engine->getToken();
    }

    public static function getTokenName() {
        $engine = static::getEngine();
        return $engine->getTokenName();
    }

    public static function getEngine() {
        if (self::$engine === null) {
            $configName = 'hyperframework.web.csrf_protection.engine_class';
            $class = Config::getString($configName , '');
            if ($class === '') {
                self::$engine = new CsrfProtectionEngine;
            } else {
                if (class_exists($class) === false) {
                    throw new ClassNotFoundException(
                        "Csrf protection engine class '$class' does not exist"
                            . ", configured using '$configName'."
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
