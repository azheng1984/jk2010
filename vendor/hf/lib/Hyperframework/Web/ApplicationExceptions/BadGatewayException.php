<?php
namespace Hyperframework\Web\ApplicationExceptions;

class BadGatewayException extends Base {
    public function __construct($message = null, $previous = null) {
        parent::__construct($message, '502 Bad Gateway', $previous);
    }
}
