<?php
namespace Hyperframework\Web;

use Hyperframework\Common\Config;
use Hyperframework\Common\ClassNotFoundException;

class ResponseHeaderHelper {
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

    public static function setResponseCode($value) {
        self::getEngine()->setResponseCode($value);
    }

    public static function setCookie($name, $value, array $options = null) {
        self::getEngine()->setCookie($name, $value, $options);
    }

    public static function getResponseCode() {
        return self::getEngine()->getResponseCode();
    }

    public static function isSent(&$file = null, &$line = null) {
        return self::getEngine()->isSent();
    }

    public static function getEngine() {
        if (self::$engine === null) {
            $configName =
                'hyperframework.web.response_header_helper.engine_class';
            $class = Config::getString($configName , '');
            if ($class === '') {
                self::$engine = new ResponseHeaderHelperEngine;
            } else {
                if (class_exists($class) === false) {
                    throw new ClassNotFoundException(
                        "Response header helper engine class '$class' "
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
