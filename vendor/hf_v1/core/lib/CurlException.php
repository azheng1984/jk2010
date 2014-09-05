<?php
namespace Hyperframework;

use Exception;

class CurlException extends Exception {
    private $handleType;

    public function __construct(
        $message, $code, $handleType = 'easy', $previous = null
    ) {
        parent::__construct($message, $code, $previous);
        $this->handleType = $handleType;
    }

    public function getHandleType() {
        return $this->handleType;
    }
}
