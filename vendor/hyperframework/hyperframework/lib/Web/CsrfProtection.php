<?php
namespace Hyperframework\Web;

use Hyperframework\Common\Config;
use Hyperframework\Common\Registry;
use Hyperframework\Common\ClassNotFoundException;

class CsrfProtection {
    /**
     * @return bool
     */
    public static function isEnabled() {
        return Config::getBool(
            'hyperframework.web.csrf_protection.enable', true
        );
    }

    public static function run() {
        $engine = static::getEngine();
        $engine->run();
    }

    /**
     * @return string
     */
    public static function getToken() {
        $engine = static::getEngine();
        return $engine->getToken();
    }

    /**
     * @return string
     */
    public static function getTokenName() {
        $engine = static::getEngine();
        return $engine->getTokenName();
    }

    /**
     * @return object
     */
    public static function getEngine() {
        $engine = Registry::get('hyperframework.web.csrf_protection_engine');
        if ($engine === null) {
            $configName = 'hyperframework.web.csrf_protection.engine_class';
            $class = Config::getString($configName , '');
            if ($class === '') {
                $engine = new CsrfProtectionEngine;
            } else {
                if (class_exists($class) === false) {
                    throw new ClassNotFoundException(
                        "Class '$class' does not exist"
                            . ", set using config '$configName'."
                    );
                }
                $engine = new $class;
            }
            static::setEngine($engine);
        }
        return $engine;
    }

    /**
     * @param object $engine
     */
    public static function setEngine($engine) {
        Registry::set('hyperframework.web.csrf_protection_engine', $engine);
    }
}
