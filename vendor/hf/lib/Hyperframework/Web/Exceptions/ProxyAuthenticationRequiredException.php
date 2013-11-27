<?php
namespace Hyperframework\Web\Exceptions;

class ProxyAuthenticationRequiredException extends ApplicationException {
    private $authenticationInfo;

    public function __construct(
        $authenticationInfo, $message = null, $previous = null
    ) {
        parent::__construct(
            $message, '407 Proxy Authentication Required', $previous
        );
        $this->authenticationInfo = $authenticationInfo;
    }

    public function setHeader() {
        parent::setHeader();
        header('Proxy-Authenticate:' . $this->authenticationInfo);
    }
}
