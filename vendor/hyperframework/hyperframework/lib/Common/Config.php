<?php
namespace Hyperframework\Common;

class Config {
    public static function get($name, $default = null) {
        return static::getEngine()->get($name, $default);
    }

    public static function getString($name, $default = null) {
        return static::getEngine()->getString($name, $default);
    }

    public static function getBoolean($name, $default = null) {
        return static::getEngine()->getBoolean($name, $default);
    }

    public static function getInt($name, $default = null) {
        return static::getEngine()->getBoolean($name, $default);
    }

    public static function getFloat($name, $default = null) {
        return static::getEngine()->getFloat($name, $default);
    }

    public static function getArray($name, $default = null) {
        return static::getEngine()->getArray($name, $default);
    }

    public static function getObject($name, $default = null) {
        return static::getEngine()->getObject($name, $default);
    }

    public static function getResource($name, $default = null) {
        return static::getEngine()->getResource($name, $default);
    }

    public static function getAppRootPath() {
        return static::getEngine()->getAppRootPath();
    }

    public static function getAppRootNamespace() {
        return static::getEngine()->getAppRootNamespace();
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

    public static function import(array $data) {
        static::getEngine()->import($data);
    }

    public static function importFile($path) {
        static::getEngine()->importFile($path);
    }

    public static function getAll() {
        return static::getEngine()->getAll();
    }

    public static function getEngine() {
        $engine = Registry::get('hyperframework.config_engine');
        if ($engine === null) {
            $engine = new ConfigEngine;
            static::setEngine($engine);
        }
        return $engine;
    }

    public static function setEngine($engine) {
        Registry::set('hyperframework.config_engine', $engine);
    }
}
