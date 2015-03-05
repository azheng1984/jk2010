<?php
namespace Hyperframework\Common\Test;

use Hyperframework\Common\ErrorHandler as Base;

class Logger {
    public static function log($level, $mixed) {
        echo __METHOD__;
    }
}
