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
            foreach ($options as $optionKey => $optionValue) {
                switch($optionKey) {
                    case 'expire':
                        $expire = $optionValue;
                        break;
                    case 'path':
                        $path = $optionValue;
                        break;
                    case 'domain':
                        $domain = $optionValue;
                        break;
                    case 'secure':
                        $secure = $optionValue;
                        break;
                    case 'httponly':
                        $httpOnly = $optionValue;
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
