<?php
namespace Hyperframework\Web\Exceptions;

class NotImplementedException extends ApplicationException {
    public function __construct($message = null, $previous = null) {
        parent::__construct($message, '501 Not Implemented', $previous);
    }
}
