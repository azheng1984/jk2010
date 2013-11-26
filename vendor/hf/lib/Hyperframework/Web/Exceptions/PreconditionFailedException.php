<?php
namespace Hyperframework\Web\Exceptions;

class PreconditionFailedException extends ApplicationException {
    public function __construct($message = null, $previous = null) {
        parent::__construct($message, '412 Precondition Failed', $previous);
    }
}
