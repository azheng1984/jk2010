<?php
namespace Hyperframework\Web;

class JsonRequestBodyParser extends RequestBodyParser {
    protected static function parse() {
        return json_decode(
            $GLOBALS['HTTP_RAW_POST_DATA'], true, 1024, JSON_BIGINT_AS_STRING
        );
    }
}
