<?php
namespace Hyperframework\Web\Exception;

class GoneException extends ApplicationException {
    public function __construct($message = null, $previous = null) {
        parent::__construct($message, '410 Gone', $previous);
    }
}
