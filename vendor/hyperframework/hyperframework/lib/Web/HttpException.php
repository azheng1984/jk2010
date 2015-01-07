<?php
namespace Hyperframework\Web;

abstract class HttpException extends WebException {
    private $statusCode;

    public function __construct($message, $statusCode, $previous) {
        parent::__construct($message, 0, $previous);
        $this->statusCode = $statusCode;
    }

    public function getStatusCode() {
        return $this->statusCode;
    }

    public function getHttpHeaders() {
        return ['HTTP/1.1 ' . $this->getStatusCode()];
    }
}
