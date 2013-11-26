<?php
namespace Hyperframework\Web\Exception;

class UnsupportedMediaTypeException extends ApplicationException {
    public function __construct($message = null, $previous = null) {
        parent::__construct($message, '415 Unsupported Media Type', $previous);
    }
}
