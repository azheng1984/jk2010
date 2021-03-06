<?php
namespace Hyperframework\Web;

use Exception;

class UnsupportedMediaTypeException extends HttpException {
    /**
     * @param string $message
     * @param Exception $previous
     */
    public function __construct($message = null, Exception $previous = null) {
        parent::__construct($message, 415, 'Unsupported Media Type', $previous);
    }
}
