<?php
namespace Hyperframework\Logging\Test;

use Hyperframework\Logging\LogHandler as Base;

class CustomLogHandler extends Base {
    public function handle($level, array $options) {
        echo __METHOD__;
    }
}
