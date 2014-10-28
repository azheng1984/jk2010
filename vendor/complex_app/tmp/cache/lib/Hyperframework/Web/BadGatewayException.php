<?php
namespace Hyperframework\Web;

class BadGatewayException extends HttpException {
    public function __construct($message = null, $previous = null) {
        parent::__construct($message, '502 Bad Gateway', $previous);
    }
}
