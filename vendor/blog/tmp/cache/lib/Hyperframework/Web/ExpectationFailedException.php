<?php
namespace Hyperframework\Web;

class ExpectationFailedException extends AppException {
    public function __construct($message = null, $previous = null) {
        parent::__construct($message, '417 Expectation Failed', $previous);
    }
}
