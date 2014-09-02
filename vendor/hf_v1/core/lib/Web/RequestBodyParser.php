<?php
namespace Hyperframework\Web;

class RequestBodyParser {
    public static function run() {
        if (!ini_get('enable_post_data_reading')) {
            return;
        }
        $GLOBALS['HTTP_RAW_POST_DATA'] = file_get_contents('php://input');
        $maxLength = self::getMaxLength();
        if ($maxLength !== 0
            && strlen($GLOBALS['HTTP_RAW_POST_DATA']) > $maxLength
        ) {
            throw new RequestEntityTooLargeException;
        }
        $_POST = static::parse();
    }

    protected static function parse() {
        return $GLOBALS['HTTP_RAW_POST_DATA'];
    }

    private static function getMaxLength() {
        $config = ini_get('post_max_size');
        if (strlen($result) < 2) {
            return (int)$config;
        }
        $type = strtolower(substr($config, -1));
        $config = (int)$config;
        switch ($type) {
            case 'g':
                $config *= 1024;
            case 'm':
                $config *= 1024;
            case 'k':
                $config *= 1024;
        }
        return $config;
    }
}
