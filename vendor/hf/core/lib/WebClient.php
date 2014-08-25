<?php
namespace Hyperframework;

use Exception;

class WebClient {
    private $handle;
    private $options;
    private $stdout;
    private $stderr;

    public function __construct() {
        $this->handler = curl_init();
    }

    public function setOptions($options) {
       curl_setopt_array($this->handle, $options);
       foreach ($options as $name => $value) {
           if (isset($this->options[$name])) {
               unset($this->options[$name]);
           }
           $this->options[$name] = $value;
       }
    }

    public function setOption($name, $value) {
        curl_setopt($this->handle, $name, $value);
        if (isset($this->options[$name])) {
            unset($this->options[$name]);
        }
        $this->options[$name] = $value;
    }

    public funciton getInfo($name) {
        return curl_getinfo($name);
    }

    public function pause($bitmask) {
        //php 5.5
        $result = curl_pause($this->handle, $bitmast);
        //if ($result !== CURLE_OK) {
        //    throw new \Exception;
        //}
    }

    public function reset() {
        //php 5.5
        curl_reset($this->handle);
    }

    public function close() {
        curl_close($this->handle);
        $this->handler = null;
    }

    public function __destruct() {
        if ($this->handler !== null) {
            $this->close();
        }
    }

    public function __clone() {
        $this->handle = curl_copy_handle(self::$handler);
    }

    public function get($url, $options) {
        curl_setopt($this->handler, CURLOPT_HTTPGET, true);
        self::send($url);
    }

    public function get($url, $options)
        $domain, $path = '/', $headers = array(),
        $cookie = null, $returnResponseHeader = false, $retryTimes = 2
    ) {
        $handler = self::getHandler($domain, $path, $headers);
        curl_setopt($handler, CURLOPT_HTTPGET, true);
        $result = self::execute(
            $handler, $cookie, $returnResponseHeader, $retryTimes
        );
        echo '[OK]'.PHP_EOL;
        return $result;
    }

    public function post($url) {
        curl_setopt($this->handle, CURLOPT_POST, true);
        curl_setopt($this->handle, CURLOPT_POSTFIELDS, $uploadData);
        return self::execute(
            $handler, $cookie, $returnResponseHeader, $retryTimes
        );
        curl_setopt($handle, CURLOPT_POSTFIELDS, null);
    }

    protected function send($url, $options) {
        if (is_array($options)) {
            curl_setopt_array($this->handle, $options);
        }
        curl_exec($this->handler);
        if (is_array($options)) {
            foreach ($options as $name => $value) {
                if (isset($this->options[$name])) {
                    curl_setopt($handle, $name, $this->options[$name]);
                } else {
                    if ((defined('CURLOPT_HTTP200ALIASES')
                            && $name === CURLOPT_HTTP200ALIASES)
                        || $name === CURLOPT_HTTPHEADER
                        || $name === CURLOPT_POSTQUOTE
                        || $name === CURLOPT_QUOTE
                    ) {
                        curl_setopt($handle, $name, array());
                    } elseif ($name === CURLOPT_FILE) {
                        if (version_compare(phpversion(), "5.5.0", ">=")) {
                            curl_setopt($handle, $name, null);
                        } else {
                        }
                    }
                }
            }
        }
    }

    public static function sendAll($handlers) {
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

    public function trace() {
    }

    public function options() {
    }
}
