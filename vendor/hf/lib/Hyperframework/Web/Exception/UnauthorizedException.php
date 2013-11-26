<?php
namespace Hyperframework\Web\Exception;

class UnauthorizedException extends ApplicationException {
    public function __construct($message = null, $previous = null) {
        parent::__construct($message, '401 Unauthorized', $previous);
    }
}
