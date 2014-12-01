<?php
namespace Hyperframework\Web;

use ErrorException as InternalErrorException;

class ErrorException extends Exception {
    private $internalErrorException;

    public function __construct() {
    }
}
