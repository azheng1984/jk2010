<?php
namespace Hyperframework\Web;

class BadRequestException extends HttpException {
    public function __construct($message = null, $previous = null) {
        parent::__construct($message, '400 Bad Request', $previous);
    }
}
