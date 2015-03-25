<?php
namespace Hyperframework\Cli\Test;

use Hyperframework\Cli\Command as Base;

class RepeatableOptionalArgumentCommand extends Base {
    public function execute($arg, array $arg2 = null) {
        echo __METHOD__;
    }
}
