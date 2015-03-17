<?php
namespace Hyperframework\Cli\Test;

use Hyperframework\Cli\Command as Base;

class RepeatableArgumentCommand extends Base {
    public function execute(array $args) {
        echo __METHOD__;
    }
}
