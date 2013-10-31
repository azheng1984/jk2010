<?php
namespace Hyperframework\Web;

class MethodNotAllowedException extends ApplicationException {
    private $methods;

    public function __construct($methods, $message = null, $previous = null) {
        parent::__construct($message, '405 Method Not Allowed', $previous);
        $this->methods = $methods;
    }

    public function sendHeader() {
        parent::sendHeader();
        header('Allow: ' . implode(', ', $this->methods));
    }
}
