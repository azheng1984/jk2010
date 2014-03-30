<?php
namespace Hyperframework;

final class ApplicationPath {
    private $applicationPath;

    public static function get() {
        if (self::$applicationPath === null) {
            self::$applicationPath = Config::get(
                __NAMESPACE__ . '.application_path',
                array(
                    'default_application_const' => 'ROOT_PATH',
                    'is_nullable' => false
                )
            );
        }
        return self::$applicationPath;
    }
}
