<?php
namespace Hyperframework\Web;

use Exception;

abstract class HttpException extends Exception {
    private $statusCode;
    private $statusText;

    /**
     * @param string $message
     * @param int $statusCode
     * @param string $statusText
     * @param Exception $previous
     */
    public function __construct(
        $message, $statusCode, $statusText, Exception $previous = null
    ) {
        parent::__construct($message, 0, $previous);
        $this->statusCode = $statusCode;
        $this->statusText = $statusText;
    }

    public function getStatus() {
        return $this->statusCode . ' ' . $this->statusText;
    }

    public function getStatusCode() {
        return $this->statusCode;
    }

    public function getStatusText() {
        return $this->statusText;
    }

    public function getHttpHeaders() {
        return ['HTTP/1.1 ' . $this->getStatus()];
    }
}
