<?php
namespace Hyperframework\Web;

class NotAcceptableException extends AppException {
    public function __construct($message = null, $previous = null) {
        parent::__construct($message, '406 Not Acceptable', $previous);
    }
}
