<?php
namespace Hyperframework\Web;

use Hyperframework\Common\Config;
use Hyperframework\Common\ClassNotFoundException;

class CsrfProtectionProvider {
    private $tokenName;
    private $token;

    public function run() {
        if ($this->getToken() === null) {
            $this->initializeToken();
        }
        if ($this->isSafeMethod($_SERVER['REQUEST_METHOD'])) {
            return;
        }
        if ($this->isValid() === false) {
            $this->initializeToken();
            throw new ForbiddenException;
        }
    }

    public function getToken() {
        if ($this->token === null) {
            $name = $this->getTokenName();
            if (isset($_COOKIE[$name])) {
                $this->token = $_COOKIE[$name];
            } else {
                $this->token = false;
            }
        }
        if ($this->token === false) {
            return;
        }
        return $this->token;
    }

    public function getTokenName() {
        if ($this->tokenName === null) {
            $this->tokenName = Config::getString(
                'hyperframework.web.csrf_protection.token_name', ''
            );
            if ($this->tokenName === '') {
                $this->tokenName = '_csrf_token';
            }
        }
        return $this->tokenName;
    }

    protected function initializeToken() {
        $this->token = $this->generateToken();
        $name = $this->getTokenName();
        setcookie($name, $this->token, 0, '/');
    }

    protected function isValid() {
        $tokenName = $this->getTokenName();
        return isset($_POST[$tokenName]) && $_POST[$tokenName] === $this->token;
    }

    protected function isSafeMethod($method) {
        return in_array($method, ['GET', 'HEAD', 'OPTIONS']);
    }

    protected function generateToken() {
        return sha1(uniqid(mt_rand(), true));
    }
}
