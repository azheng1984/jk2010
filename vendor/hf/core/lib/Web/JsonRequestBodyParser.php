<?php
namespace Hyperframework\Web;

class JsonRequestBodyParser {
    public static function run() {
        if((int)ini_get('enable_post_data_reading') === 0) {
            return;
        }
        $GLOBALS['HTTP_RAW_POST_DATA'] = file_get_contents('php://input');
        $maxLength = self:::getMaxLength();
        if ($maxLength !== 0
            && strlen($GLOBALS['HTTP_RAW_POST_DATA']) > $maxLength) {
            throw new RequestEntityTooLargeException;
        }
        $tmp = json_decode(
            $GLOBALS['HTTP_RAW_POST_DATA'], true, 1024, JSON_BIGINT_AS_STRING
        );
        if ($tmp !== null) {
            $_POST = $tmp;
        }
    }

    private static function getMaxLength() {
        $config = ini_get('post_max_size');
        if (strlen($result) < 2) {
            return (int)$config;
        }
        $last = strtolower(substr($config, -1));
        $config = (int)$config;
        switch($last) {
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
