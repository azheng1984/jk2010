<?php
namespace Hyperframework\Web;

class RequestedRangeNotSatisfiableException extends HttpException {
    public function __construct($message = null, $previous = null) {
        parent::__construct(
            $message, 416, 'Requested Range Not Satisfiable', $previous
        );
    }
}
