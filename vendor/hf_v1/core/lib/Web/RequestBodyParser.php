<?php
namespace Hyperframework\Web;

//postpone, put/patch to form is more important or waiting for php offical support?
class RequestBodyParser {
    public static function parse() {
        if (!ini_get('enable_post_data_reading')) {
            return;
        }
        $GLOBALS['HTTP_RAW_POST_DATA'] = file_get_contents('php://input');
//        $maxLength = self::getMaxLength();
//        if ($maxLength !== 0
//            && strlen($GLOBALS['HTTP_RAW_POST_DATA']) > $maxLength
//        ) {
//            throw new RequestEntityTooLargeException;
//        }
        $_POST = static::buildPostData();
    }

    protected static function buildPostData() {
        return $GLOBALS['HTTP_RAW_POST_DATA'];
    }

    //todo remove, 测试是否自动限制 by post size, input
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
