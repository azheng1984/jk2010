<?php
namespace Hyperframework\Web;

class NotFoundException extends HttpException {
    public function __construct($message = '', $previous = null) {
        parent::__construct($message, '404 Not Found', $previous);
    }
}
