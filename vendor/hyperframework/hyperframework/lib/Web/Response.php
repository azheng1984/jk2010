<?php
namespace Hyperframework\Web;

use Hyperframework\Common\Registry;
use Hyperframework\Common\Config;
use Hyperframework\Common\ClassNotFoundException;

class Response {
    public static function setHeader(
        $string, $shouldReplace = true, $responseCode = null
    ) {
        self::getEngine()->setHeader($string, $shouldReplace, $responseCode);
    }

    public static function getHeaders() {
        return self::getEngine()->getHeaders();
    }

    public static function removeHeader($name) {
        self::getEngine()->removeHeader($name);
    }

    public static function removeHeaders() {
        self::getEngine()->removeHeaders();
    }

    public static function setStatusCode($statusCode) {
        self::getEngine()->setStatusCode($statusCode);
    }

    public static function getStatusCode() {
        return self::getEngine()->getStatusCode();
    }

    public static function setCookie($name, $value, array $options = null) {
        self::getEngine()->setCookie($name, $value, $options);
    }

    public static function headersSent(&$file = null, &$line = null) {
        return self::getEngine()->headersSent();
    }

    public static function getEngine() {
        $engine = Registry::get('hyperframework.web.response_engine');
        if ($engine === null) {
            $configName = 'hyperframework.web.response_engine_class';
            $class = Config::getString($configName , '');
            if ($class === '') {
                $engine = new ResponseEngine;
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

    public static function setEngine($engine) {
        Registry::set('hyperframework.web.response_engine', $engine);
    }
}
