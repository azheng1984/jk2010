<?php
namespace Hyperframework\Web\Exceptions;

class LengthRequiredException extends ApplicationException {
    public function __construct($message = null, $previous = null) {
        parent::__construct($message, '411 Length Required', $previous);
    }
}
