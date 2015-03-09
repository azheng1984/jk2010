<?php
namespace Hyperframework\Web;

use Hyperframework\Common\Config;
use Hyperframework\Common\ClassNotFoundException;

class Request {
    public static function getAllHeaders() {
        return getallheaders();
    }

    public static function openInputStream() {
        return fopen('php://input');
    }
}