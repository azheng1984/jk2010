<?php
namespace Hyperframework\Web\Exceptions;

class RequestedRangeNotSatisfiableException extends ApplicationException {
    public function __construct($message = null, $previous = null) {
        parent::__construct(
            $message, '416 Requested Range Not Satisfiable', $previous
        );
    }
}
