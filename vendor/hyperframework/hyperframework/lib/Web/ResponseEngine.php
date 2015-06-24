<?php
namespace Hyperframework\Web;

class ResponseEngine {
    /**
     * @param string $string
     * @param bool $shouldReplace
     * @param int $responseCode
     */
    public function setHeader(
        $string, $shouldReplace = true, $responseCode = null
    ) {
        header($string, $shouldReplace, $responseCode);
    }

    /**
     * @return string[]
     */
    public function getHeaders() {
        return headers_list();
    }

    /**
     * @param string $name
     */
    public function removeHeader($name) {
        header_remove($name);
    }

    public function removeHeaders() {
        header_remove();
    }

    /**
     * @param int $statusCode
     */
    public function setStatusCode($statusCode) {
        http_response_code($statusCode);
    }

    /**
     * @return int
     */
    public function getStatusCode() {
        return http_response_code();
    }

    /**
     * @param string $name
     * @param string $value
     * @param array $options
     */
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
                            "Option '$optionKey' is not allowed."
                        );
                }
            }
        }
        setcookie(
            $name, $value, $expire, $path, $domain, $secure, $httpOnly
        );
    }

    /**
     * @param string $file
     * @param int $line
     * @return bool
     */
    public function headersSent(&$file = null, &$line = null) {
        return headers_sent($file, $line);
    }
}
