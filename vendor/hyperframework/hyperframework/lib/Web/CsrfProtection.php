<?php
namespace Hyperframework\Web;

use Exception;
use Hyperframework\Common\Config;

class CsrfProtection {
    private static $isEnabled = false;
    private static $token;

    public static function run() {
        self::$isEnabled = true;
        static::initializeToken();
        if (in_array($_SERVER['REQUEST_METHOD'], static::getSafeMethods())) {
            return;
        }
        if (static::isValid() === false) {
            //todo reset token
            throw new Exception;
        }
    }

    public static function isEnabled() {
        return self::$isEnabled;
    }

    public static function getToken() {
        return self::$token;
    }

    public static function getTokenName() {
        $name = Config::get('hyperframework.web.csrf_protection.token_name');
        if ($name === null) {
            return '_csrf_token';
        }
        return $name;
    }

    protected static function initializeToken() {
        $name = self::getTokenName();
        if (isset($_COOKIE[$name])) {
            self::$token = $_COOKIE[$name];
            return;
        }
        self::$token = static::generateToken();
        setcookie($name, self::$token, null, null, null, false, true);
    }

    protected static function isValid() {
        $tokenName = self::getTokenName();
        return isset($_POST[$tokenName]) && $_POST[$tokenName] === self::$token;
    }

    protected static function getSafeMethods() {
        return array('GET', 'HEAD', 'OPTIONS');
    }

    protected static function generateToken() {
        return sha1(uniqid(mt_rand(), true));
    }

    protected static function setToken($value) {
        self::$token = $value;
    } 
}
