<?php
namespace Hyperframework\Web\Exception;

class NotImplementedException extends ApplicationException {
    public function __construct($message = null, $previous = null) {
        parent::__construct($message, '501 Not Implemented', $previous);
    }
}
