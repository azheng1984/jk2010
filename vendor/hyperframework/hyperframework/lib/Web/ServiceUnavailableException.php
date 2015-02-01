<?php
namespace Hyperframework\Web;

class ServiceUnavailableException extends HttpException {
    public function __construct($message = null, $previous = null) {
        parent::__construct($message, 503 'Service Unavailable', $previous);
    }
}
