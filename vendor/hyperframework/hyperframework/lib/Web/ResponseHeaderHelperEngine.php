<?php
namespace Hyperframework\Web;

class ResponseHeaderHelperEngine {
    public function setHeader(
        $string, $shouldReplace = true, $responseCode = null
    ) {
        header($string, $shouldReplace, $responseCode);
    }

    public function getHeaders() {
        return headers_list();
    }

    public function removeHeader($name) {
        header_remove($name);
    }

    public function removeAllHeaders() {
        header_remove();
    }

    public function setResponseCode($code) {
        http_response_code($code);
    }

    public function getResponseCode() {
        return http_response_code();
    }

    public function setCookie(
        $name, $value, $expire = 0, $path = null,
        $domain = null, $secure = false, $httpOnly = false
    ) {
        setcookie(
            $name, $value, $expire, $path, $domain, $secure, $httpOnly
        );
    }

    public function setRawCookie(
        $name, $value, $expire = 0, $path = null,
        $domain = null, $secure = false, $httpOnly = false
    ) {
        setrawcookie(
            $name, $value, $expire, $path, $domain, $secure, $httpOnly
        );
    }

    public function isSent(&$file = null, &$line = null) {
        return headers_sent($file, $line);
    }
}
