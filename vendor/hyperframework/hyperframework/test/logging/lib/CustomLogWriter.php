<?php
namespace Hyperframework\Logging\Test;

use Hyperframework\Logging\LogHandler as Base;

class CustomLogWriter extends Base {
    public function write($text) {
        echo __METHOD__;
    }
}
