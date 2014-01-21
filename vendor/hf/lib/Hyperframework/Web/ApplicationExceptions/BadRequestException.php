<?php
namespace Hyperframework\Web\Exceptions;

class BadRequestException extends ApplicationException {
    public function __construct($message = null, $previous = null) {
        parent::__construct($message, '400 Bad Request', $previous);
    }
}
