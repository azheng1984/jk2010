<?php
namespace Hyperframework\Web;

use Exception;

abstract class HttpException extends Exception {
    public function __construct($message, $statusCode, $previous) {
        parent::__construct($message, null, $previous);
        $this->code = $statusCode;
    }

    public function getHttpHeaders() {
        return ['HTTP/1.1 ' . $this->code];
    }
}
