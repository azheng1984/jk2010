<?php
namespace Hyperframework\Web;

use Exception;

class MethodNotAllowedException extends HttpException {
    private $methods;

    /**
     * @param string[] $methods
     * @param string $message
     * @param Exception $previous
     */
    public function __construct(
        array $methods = ['GET', 'HEAD'],
        $message = null,
        Exception $previous = null
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
