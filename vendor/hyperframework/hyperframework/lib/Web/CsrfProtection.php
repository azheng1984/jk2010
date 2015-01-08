<?php
namespace Hyperframework\Web;

use Hyperframework\Common\Config;

class CsrfProtection {
    private static $isEnabled;
    private static $tokenName;
    private static $token;

    public static function run() {
        if (static::isEnabled()) {
            if (static::getToken() === null) {
                static::initializeToken();
            }
            if (static::isSafeMethod($_SERVER['REQUEST_METHOD'])) {
                return;
            }
            if (static::isValid() === false) {
                static::initializeToken();
                throw new ForbiddenException;
            }
        }
    }

    public static function isEnabled() {
        if (self::$isEnabled === null) {
            self::$isEnabled = Config::getBoolean(
                'hyperframework.web.csrf_protection.enable', true
            );
        };
        return self::$isEnabled;
    }

    public static function getToken() {
        if (self::$token === null) {
            $name = static::getTokenName();
            if (isset($_COOKIE[$name])) {
                self::$token = $_COOKIE[$name];
            } else {
                self::$token = false;
            }
        }
        if (self::$token === false) {
            return;
        }
        return self::$token;
    }

    public static function getTokenName() {
        if (self::$tokenName === null) {
            self::$tokenName = Config::getString(
                'hyperframework.web.csrf_protection.token_name', ''
            );
            if (self::$tokenName === '') {
                self::$tokenName = '_csrf_token';
            }
        }
        return self::$tokenName;
    }

    protected static function initializeToken() {
        self::$token = static::generateToken();
        $name = self::getTokenName();
        setcookie($name, self::$token, null, null, null, false, true);
    }

    protected static function isValid() {
        $tokenName = self::getTokenName();
        return isset($_POST[$tokenName]) && $_POST[$tokenName] === self::$token;
    }

    protected static function isSafeMethod($method) {
        return in_array($method, ['GET', 'HEAD', 'OPTIONS']);
    }

    protected static function generateToken() {
        return sha1(uniqid(mt_rand(), true));
    }
}
