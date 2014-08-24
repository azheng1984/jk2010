<?php
namespace Hyperframework;

use Exception;

class WebClient {
    private static $handlers = array();

    public function get(
        $domain, $path = '/', $headers = array(),
        $cookie = null, $returnResponseHeader = false, $retryTimes = 2
    ) {
        echo $domain.$path;
        $handler = self::getHandler($domain, $path, $headers);
        curl_setopt($handler, CURLOPT_HTTPGET, true);
        $result = self::execute(
            $handler, $cookie, $returnResponseHeader, $retryTimes
        );
        echo '[OK]'.PHP_EOL;
        return $result;
    }

    public function post(
        $domain, $path = '/', $uploadData = null, $headers = array(),
        $cookie = null, $returnResponseHeader = false, $retryTimes = 0
    ) {
        $handler = self::getHandler($domain, $path, $headers);
        curl_setopt($handler, CURLOPT_POST, true);
        curl_setopt($handler, CURLOPT_POSTFIELDS, $uploadData);
        return self::execute(
            $handler, $cookie, $returnResponseHeader, $retryTimes
        );
    }

    public function sendRequest($method) {
    }

    public function close() {
        foreach (self::$handlers as $handler) {
            curl_close($handler);
        }
        self::$handlers = array();
    }

    private static function getHandler($domain, $path, $headers) {
        if (!isset(self::$handlers[$domain])) {
            $handler = curl_init();
            //curl_setopt($handler, CURLOPT_HTTPHEADER, array('Accept-Encoding: gzip'));
            //curl_setopt($handler, CURLOPT_ENCODING, 'gzip');
            curl_setopt($handler, CURLOPT_TIMEOUT, 30);
            curl_setopt($handler, CURLOPT_BINARYTRANSFER, 1);
            curl_setopt($handler, CURLOPT_FOLLOWLOCATION, 1);
            curl_setopt($handler, CURLOPT_RETURNTRANSFER, 1);
            self::$handlers[$domain] = $handler;
        }
        $handler = self::$handlers[$domain];
        $headers[] = 'Accept: */*';
        $headers[] = 'Accept-Language: zh-CN';
        $headers[] = 'Accept-Encoding: gzip';
        //$headers[] = 'User-Agent: Mozilla/5.0 '
        //  .'(compatible; bingbot/2.0; +http://www.bing.com/bingbot.htm)';
        curl_setopt($handler, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($handler, CURLOPT_URL, 'http://'.$domain.$path);
        return $handler;
    }

    private static function execute(
        $handler, $cookie, $returnResponseHeader, $retryTimes
    ) {
        curl_setopt($handler, CURLOPT_COOKIE, $cookie);
        if ($returnResponseHeader) {
            curl_setopt($handler, CURLOPT_HEADER, 1);
        }
        $content = curl_exec($handler);
        curl_setopt($handler, CURLOPT_HEADER, 0);
        if ($content === false && $retryTimes > 0) {
            return self::execute(
                $handler, $cookie, $returnResponseHeader, --$retryTimes
            );
        }
        if ($content === false) {
            throw new Exception(null, 500);
        }
        $result = curl_getinfo($handler);
        if ($result['http_code'] >= 400) {
            throw new Exception(null, $result['http_code']);
        }
        if ($returnResponseHeader) {
            list($header, $data) = explode("\r\n\r\n", $content, 2);
            $content = $data;
            $result['header'] = $header;
        }
        $result['content'] = $content;
        return $result;
    }

    public function patch() {
    }

    public function put() {
    }

    public function delete() {
    }

    public function head() {
    }

    public function options() {
    }


}
