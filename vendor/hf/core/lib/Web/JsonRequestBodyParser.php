<?php
namespace Hyperframework\Web;

class JsonRequestBodyParser {
    public static function run() {
        $GLOBALS['HTTP_RAW_POST_DATA'] = file_get_contents('php://input');
        $_POST = json_decode($GLOBALS['HTTP_RAW_POST_DATA']);
    }
}
