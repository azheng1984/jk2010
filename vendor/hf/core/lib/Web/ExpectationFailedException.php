<?php
namespace Hyperframework\Web\Exceptions;

class ExpectationFailedException extends ApplicationException {
    public function __construct($message = null, $previous = null) {
        parent::__construct($message, '417 Expectation Failed', $previous);
    }
}
