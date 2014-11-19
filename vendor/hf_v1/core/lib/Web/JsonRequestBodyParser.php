<?php
namespace Hyperframework\Web;

class JsonRequestBodyParser extends RequestBodyParser {
    protected static function buildPostData() {
        if ($GLOBALS['HTTP_RAW_POST_DATA'] === '') {
            return;
        }
        $result = json_decode(
            $GLOBALS['HTTP_RAW_POST_DATA'], true, 1024, JSON_BIGINT_AS_STRING
        );
        if (json_last_error() !== JSON_ERROR_NONE) {
            //throw exception?
        }
        return $result;
    }
}
