<?php
namespace Hyperframework\Web;

class ServiceUnavailableException extends ApplicationException {
    public function __construct($message = null, $previous = null) {
        parent::__construct($message, '503 Service Unavailable', $previous);
    }
}
