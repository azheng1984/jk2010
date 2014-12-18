<?php
namespace Hyperframework\Web;

use Exception;

abstract class HttpException extends Exception {
    private $statusCode;

    public function __construct($message, $statusCode, $previous) {
        $tmp = explode(' ', $statusCode, 2);
        parent::__construct($message, (int)$tmp[0], $previous);
        $this->statusCode = $statusCode;
    }

    public function getStatusCode() {
        return $this->statusCode;
    }

    public function getHttpHeaders() {
        return ['HTTP/1.1 ' . $this->getStatusCode()];
    }
}
