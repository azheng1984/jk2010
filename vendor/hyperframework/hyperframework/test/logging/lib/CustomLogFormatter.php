<?php
namespace Hyperframework\Logging\Test;

use Hyperframework\Logging\LogHandler as Base;

class CustomLogFormatter extends Base {
    public function format($record) {
        echo __METHOD__;
    }
}
