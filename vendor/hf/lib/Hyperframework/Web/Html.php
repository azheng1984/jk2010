<?php
namespace Hyperframework\Web;

//html helper
//includes:
//form helper
//head helper
//other html tag helper

class Html {
    private $bindingStack;

    //auto binding to current request method params
    public static function beginBinding($data = null, $errors = null) {
    }

    public static function endBinding() {
    }

    //auto binding
    public static function beginForm($options) {
    }

    public static function endForm() {
    }
}
