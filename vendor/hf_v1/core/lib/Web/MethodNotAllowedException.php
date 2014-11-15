<?php
namespace Hyperframework\Web;

class MethodNotAllowedException extends HttpException {
    private $methods;

    public function __construct(
        array $methods = ['GET', 'HEAD'], $message = null, $previous = null
    ) {
        parent::__construct($message, '405 Method Not Allowed', $previous);
        $this->methods = $methods;
    }

    public function sendHeader() {
        parent::sendHeader();
        if ($this->methods !== null) {
            header('Allow: ' . implode(', ', $this->methods));
        }
    }
}
