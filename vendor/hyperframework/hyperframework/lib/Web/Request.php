<?php
namespace Hyperframework\Web;

use Hyperframework\Common\Registry;
use Hyperframework\Common\Config;
use Hyperframework\Common\ClassNotFoundException;

class Request {
    /**
     * @return string[]
     */
    public static function getHeaders() {
        return self::getEngine()->getHeaders();
    }

    /**
     * @return resource
     */
    public static function openInputStream() {
        return self::getEngine()->openInputStream();
    }

    /**
     * @return object
     */
    public static function getEngine() {
        $engine = Registry::get('hyperframework.web.request_engine');
        if ($engine === null) {
            $configName = 'hyperframework.web.request_engine_class';
            $class = Config::getString($configName , '');
            if ($class === '') {
                $engine = new RequestEngine;
            } else {
                if (class_exists($class) === false) {
                    throw new ClassNotFoundException(
                        "Class '$class' does not exist, "
                            . "set using config '$configName'."
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
        Registry::set('hyperframework.web.request_engine', $engine);
    }
}
