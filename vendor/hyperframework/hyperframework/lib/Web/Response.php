<?php
namespace Hyperframework\Web;

use Hyperframework\Common\Config;
use Hyperframework\Common\ClassNotFoundException;

class Response {
    private static $engine;

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

    public static function removeAllHeaders() {
        self::getEngine()->removeAllHeaders();
    }

    public static function setStatusCode($value) {
        self::getEngine()->setStatusCode($value);
    }

    public static function getStatusCode() {
        return self::getEngine()->getResponseCode();
    }

    public static function setCookie($name, $value, array $options = null) {
        self::getEngine()->setCookie($name, $value, $options);
    }

    public static function headersSent(&$file = null, &$line = null) {
        return self::getEngine()->headersSent();
    }

    public static function getEngine() {
        if (self::$engine === null) {
            $configName =
                'hyperframework.web.response.engine_class';
            $class = Config::getString($configName , '');
            if ($class === '') {
                self::$engine = new ResponseHeaderEngine;
            } else {
                if (class_exists($class) === false) {
                    throw new ClassNotFoundException(
                        "Response engine class '$class' "
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
