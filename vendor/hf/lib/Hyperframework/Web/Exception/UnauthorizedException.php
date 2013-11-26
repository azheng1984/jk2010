<?php
namespace Hyperframework\Web\Exception;

class UnauthorizedException extends ApplicationException {
    private $authenticationInfo;

    public function __construct(
        $authenticationInfo, $message = null, $previous = null
    ) {
        parent::__construct($message, '401 Unauthorized', $previous);
    }

    public function setHeader() {
        parent::setHeader();
        header('WWW-Authenticate:' . $this->authenticationInfo);
    }
}
