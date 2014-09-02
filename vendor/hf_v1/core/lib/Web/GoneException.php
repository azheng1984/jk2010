<?php
namespace Hyperframework\Web;

class GoneException extends HttpException {
    public function __construct($message = null, $previous = null) {
        parent::__construct($message, '410 Gone', $previous);
    }
}
