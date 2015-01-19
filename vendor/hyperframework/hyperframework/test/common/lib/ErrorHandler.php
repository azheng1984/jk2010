<?php
namespace Hyperframework\Common\Test;

use Hyperframework\Common\ErrorHandler as Base;

class ErrorHandler extends Base {
    public function callProtectedMethod($method, $args = []) {
        return call_user_func_array([$this, $method], $args);
    }
}
