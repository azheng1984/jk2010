<?php
namespace Hyperframework\Web\Exception;

class BadGatewayException extends ApplicationException {
    public function __construct($message = null, $previous = null) {
        parent::__construct($message, '502 Bad Gateway', $previous);
    }
}
