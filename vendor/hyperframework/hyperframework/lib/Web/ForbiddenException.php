<?php
namespace Hyperframework\Web;

class ForbiddenException extends HttpException {
    public function __construct($message = null, $previous = null) {
        parent::__construct($message, 403, 'Forbidden', $previous);
    }
}
