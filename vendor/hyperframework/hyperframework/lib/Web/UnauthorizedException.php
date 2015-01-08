<?php
namespace Hyperframework\Web;

class UnauthorizedException extends HttpException {
    private $authenticationInfo;

    public function __construct(
        $authenticationInfo, $message = null, $previous = null
    ) {
        parent::__construct($message, '401 Unauthorized', $previous);
        $this->authenticationInfo = $authenticationInfo;
    }

    public function setHeader() {
        $headers = parent::getHttpHeaders();
        $headers[] = 'WWW-Authenticate: ' . $this->authenticationInfo;
        return $headers;
    }
}
