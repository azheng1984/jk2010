<?php
namespace Hyperframework\Web;

use Hyperframework\Common\Config;
use Hyperframework\Common\ClassNotFoundException;

class CsrfProtection {
    private static $provider;

    public static function run() {
        if (static::isEnabled()) {
            $provider = self::getProvider();
            $provider->run();
        }
    }

    public static function isEnabled() {
        return Config::getBoolean(
            'hyperframework.web.csrf_protection.enable', true
        );
    }

    public static function getToken() {
        $provider = self::getProvider();
        return $provider->getToken();
    }

    public static function getTokenName() {
        $provider = self::getProvider();
        return $provider->getTokenName();
    }

    public static function getProvider() {
        if (self::$provider === null) {
            $configName = 'hyperframework.web.csrf_protection.provider_class';
            $class = Config::getString($configName , '');
            if ($class === '') {
                self::$provider = new CsrfProtectionProvider;
            } else {
                if (class_exists($class) === false) {
                    throw new ClassNotFoundException(
                        "Csrf protection provider class '$class' does not exist"
                            . ", defined in '$configName'."
                    );
                    self::$provider = new $class;
                }
            }
        }
        return self::$provider;
    }

    public static function setProvider($value) {
        self::$provider = $value;
    }
}
