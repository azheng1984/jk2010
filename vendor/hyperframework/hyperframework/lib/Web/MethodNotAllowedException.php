<?php
namespace Hyperframework\Web;

class MethodNotAllowedException extends HttpException {
    private $methods;

    public function __construct(
        array $methods = ['GET', 'HEAD'], $message = null, $previous = null
    ) {
        parent::__construct($message, 405, 'Method Not Allowed', $previous);
        $this->methods = $methods;
    }

    public function getHttpHeaders() {
        $headers = parent::getHttpHeaders();
        if (count($this->methods) !== 0) {
            $headers[] = 'Allow: ' . implode(', ', $this->methods);
        }
        return $headers;
    }
}
