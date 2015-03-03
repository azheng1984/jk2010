<?php
namespace Hyperframework\Web;

use Hyperframework\Common\Config;
use Hyperframework\Common\InvalidOperationException;

class CsrfProtectionEngine {
    private $tokenName;
    private $token;

    public function run() {
        $tokenName = $this->getTokenName();
        if ($this->isSafeMethod($_SERVER['REQUEST_METHOD'])) {
            if (isset($_COOKIE[$tokenName]) === false) {
                $this->initializeToken();
            }
        } else {
            if (isset($_COOKIE[$tokenName]) === false) {
                throw new ForbiddenException;
            } else {
                if (isset($_POST[$tokenName]) === false
                    || $_POST[$tokenName] !== $_COOKIE[$tokenName]
                ) {
                    $this->initializeToken();
                    throw new ForbiddenException;
                }
            }
        }
    }

    public function getToken() {
        if ($this->token === null) {
            $name = $this->getTokenName();
            if (isset($_COOKIE[$name])) {
                $this->setToken($_COOKIE[$name]);
            } else {
                throw new InvalidOperationException(
                    'Csrf protection is not initialized correctly.'
                );
            }
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

    protected function setToken($token) {
        $this->token = $token;
    }

    protected function initializeToken() {
        $this->setToken($this->generateToken());
        ResponseHeaderHelper::setCookie(
            $this->getTokenName(), $this->getToken()
        );
    }

    protected function isSafeMethod($method) {
        return in_array($method, ['GET', 'HEAD', 'OPTIONS']);
    }

    protected function generateToken() {
        return sha1(uniqid(mt_rand(), true));
    }
}
