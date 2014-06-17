<?php
namespace Hyperframework\Web;

class NotFoundException extends AppException {
    public function __construct($message = null, $previous = null) {
        parent::__construct($message, '404 Not Found', $previous);
    }
}
