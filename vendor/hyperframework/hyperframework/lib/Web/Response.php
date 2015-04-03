<?php
namespace Hyperframework\Web;

use Hyperframework\Common\Registry;
use Hyperframework\Common\Config;
use Hyperframework\Common\ClassNotFoundException;

class Response {
    /**
     * @param string $string
     * @param bool $shouldReplace
     * @param int $responseCode
     */
    public static function setHeader(
        $string, $shouldReplace = true, $responseCode = null
    ) {
        self::getEngine()->setHeader($string, $shouldReplace, $responseCode);
    }

    /**
     * @return string[]
     */
    public static function getHeaders() {
        return self::getEngine()->getHeaders();
    }

    /**
     * @param string $name
     */
    public static function removeHeader($name) {
        self::getEngine()->removeHeader($name);
    }

    public static function removeHeaders() {
        self::getEngine()->removeHeaders();
    }

    /**
     * @param int $statusCode
     */
    public static function setStatusCode($statusCode) {
        self::getEngine()->setStatusCode($statusCode);
    }

    /**
     * @return int
     */
    public static function getStatusCode() {
        return self::getEngine()->getStatusCode();
    }

    /**
     * @param string $name
     * @param string $value
     * @param array $options
     */
    public static function setCookie($name, $value, array $options = null) {
        self::getEngine()->setCookie($name, $value, $options);
    }

    /**
     * @param string $file
     * @param int $line
     * @return bool
     */
    public static function headersSent(&$file = null, &$line = null) {
        return self::getEngine()->headersSent();
    }

    /**
     * @return object
     */
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

    /**
     * @param object $engine
     */
    public static function setEngine($engine) {
        Registry::set('hyperframework.web.response_engine', $engine);
    }
}
