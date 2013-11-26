<?php
namespace Hyperframework\Web\Exception;

class RequestTimeoutException extends ApplicationException {
    public function __construct($message = null, $previous = null) {
        parent::__construct($message, '408 Request Timeout', $previous);
    }
}
