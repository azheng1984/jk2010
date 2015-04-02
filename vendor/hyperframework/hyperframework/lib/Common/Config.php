<?php
namespace Hyperframework\Common;

class Config {
    /**
     * @param string $name
     * @param mixed $default
     */
    public static function get($name, $default = null) {
        return static::getEngine()->get($name, $default);
    }

    /**
     * @param string $name
     * @param string $default
     */
    public static function getString($name, $default = null) {
        return static::getEngine()->getString($name, $default);
    }

    /**
     * @param string $name
     * @param boolean $default
     */
    public static function getBoolean($name, $default = null) {
        return static::getEngine()->getBoolean($name, $default);
    }

    /**
     * @param string $name
     * @param int $default
     */
    public static function getInt($name, $default = null) {
        return static::getEngine()->getInt($name, $default);
    }

    /**
     * @param string $name
     * @param float $default
     */
    public static function getFloat($name, $default = null) {
        return static::getEngine()->getFloat($name, $default);
    }

    /**
     * @param string $name
     * @param array $default
     */
    public static function getArray($name, $default = null) {
        return static::getEngine()->getArray($name, $default);
    }

    /**
     * @return string
     */
    public static function getAppRootPath() {
        return static::getEngine()->getAppRootPath();
    }

    /**
     * @return string
     */
    public static function getAppRootNamespace() {
        return static::getEngine()->getAppRootNamespace();
    }

    /**
     * @return array
     */
    public static function getAll() {
        return static::getEngine()->getAll();
    }

    public static function set($name, $value) {
        static::getEngine()->set($name, $value);
    }

    public static function has($name) {
        return static::getEngine()->has($name);
    }

    public static function remove($name) {
        static::getEngine()->remove($name);
    }

    public static function import($data) {
        static::getEngine()->import($data);
    }

    public static function importFile($path) {
        static::getEngine()->importFile($path);
    }

    /**
     * @return object
     */
    public static function getEngine() {
        $engine = Registry::get('hyperframework.config_engine');
        if ($engine === null) {
            $engine = new ConfigEngine;
            static::setEngine($engine);
        }
        return $engine;
    }

    /**
     * @param object $engine
     */
    public static function setEngine($engine) {
        Registry::set('hyperframework.config_engine', $engine);
    }
}
