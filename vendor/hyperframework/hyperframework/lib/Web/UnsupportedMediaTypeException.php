<?php
namespace Hyperframework\Web;

class UnsupportedMediaTypeException extends HttpException {
    public function __construct($message = null, $previous = null) {
        parent::__construct($message, 415, 'Unsupported Media Type', $previous);
    }
}
