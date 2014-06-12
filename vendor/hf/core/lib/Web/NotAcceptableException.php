<?php
namespace Hyperframework\Web\Exceptions;

class NotAcceptableException extends ApplicationException {
    public function __construct($message = null, $previous = null) {
        parent::__construct($message, '406 Not Acceptable', $previous);
    }
}
