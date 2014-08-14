<?php
namespace Hyperframework\Web;

class InternalServerErrorException extends HttpException {
    public function __construct($message = null, $previous = null) {
        parent::__construct($message, '500 Internal Server Error', $previous);
    }
}
