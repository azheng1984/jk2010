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

    public function setCookie($name, $value, array $options = null) {
        $expire = 0;
        $path = '/';
        $domain = null;
        $secure = false;
        $httpOnly = false;
        if ($options !== null) {
            foreach ($options as $key => $value) {
                switch($key) {
                    case 'expire':
                        $expire = $value;
                        break;
                    case 'path':
                        $path = $value;
                        break;
                    case 'domain':
                        $domain = $value;
                        break;
                    case 'secure':
                        $secure = $value;
                        break;
                    case 'httponly':
                        $httpOnly = $value;
                        break;
                    default:
                        throw new CookieException(
                            "Option '$key' is not allowed."
                        );
                }
            }
        }
        setcookie(
            $name, $value, $expire, $path, $domain, $secure, $httpOnly
        );
    }

    public function isSent(&$file = null, &$line = null) {
        return headers_sent($file, $line);
    }
}
