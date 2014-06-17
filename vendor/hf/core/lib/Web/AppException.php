<?php
namespace Hyperframework\Web;

abstract class AppException extends \Exception {
    public function __construct($message, $statusCode, $previous) {
        parent::__construct($message, null, $previous);
        $this->code = $statusCode;
    }

    public function setHeader() {
        header('HTTP/1.1 ' . $this->code);
    }
}
