<?php
namespace Hyperframework\Web;

class LengthRequiredHttpException extends HttpException {
    public function __construct($message = null, $previous = null) {
        parent::__construct($message, 411 'Length Required', $previous);
    }
}
