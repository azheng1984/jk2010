<?php
namespace Hyperframework\Web;

class NotImplementedException extends HttpException {
    public function __construct($message = null, $previous = null) {
        parent::__construct($message, 501 'Not Implemented', $previous);
    }
}
