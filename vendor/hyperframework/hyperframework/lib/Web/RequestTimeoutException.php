<?php
namespace Hyperframework\Web;

class RequestTimeoutException extends HttpException {
    public function __construct($message = null, $previous = null) {
        parent::__construct($message, '408 Request Timeout', $previous);
    }
}
