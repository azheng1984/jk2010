<?php
namespace Hyperframework;

use Exception;

class WebClient {
    private $handle;
    private $options = array();
    private $temporaryOptions;
    private $stdStreams;
    private $isInFileOptionDirty;
    private static $isOldCurl;

    public function __construct() {
        if (self::$isOldCurl === null) {
            self::$isOldCurl = version_compare(phpversion(), '5.5.0', '<');
        }
        $this->handle = curl_init();
    }

    public function setOptions($options) {
        curl_setopt_array($this->handle, $options);
        foreach ($options as $name => $value) {
            if ($value !== null) {
                $this->options[$name] = $value;
            } else {
                unset($this->options[$name]);
            }
            if ($this->temporaryOptions !== null) {
                unset($this->temporaryOptions[$name]);
            }
        }
        if (self::$isOldCurl) {
            $this->processDirtyInFileOption($options);
        }
    }

    public function setOption($name, $value) {
        $this->setOptions(array($name => $value));
    }

    private function processDirtyInFileOption($options) {
        if (array_key_exists($options, CURLOPT_INFILE)) {
            if ($value === null) {
                $this->isInFileOptionDirty = true;
                $this->addReadWrapper();
                continue;
            }
            $this->isInFileOptionDirty = false;
            $readCallback = $this->getReadCallback();
            if ($readCallback === null) {
                curl_setopt($this->handle, CURLOPT_READFUNCTION, null);
            } else {
                curl_setopt(
                    $this->handle, CURLOPT_READFUNCTION, $readCallback
                );
            }
        } elseif (array_key_exists($options, CURLOPT_READFUNCTION)
            && $this->isInFileOptionDirty
        ) {
            $this->addReadWrapper();
        }
    }

    private function addReadWrapper() {
        $callback = null;
        $readCallback = $this->getReadCallback();
        if ($readCallback !== null) {
            $callback = function($handle, $dirtyHandle, $maxLength)
                use ($readCallback)
            {
                return call_user_func($readCallback, $handle, null, $maxLength);
            }
        } else {
            $callback = function() {
                throw new Exception;
            };
        }
        curl_setopt($this->handle, CURLOPT_READFUNCTION, $callback);
    }

    private function getReadCallback() {
        $readCallback = null;
        if ($this->temporaryOptions !== null && array_key_exists(
            $this->temporaryOptions, CURLOPT_READFUNCTION
        )) {
            $readCallback =
                $this->temporaryOptions[CURLOPT_READFUNCTION];
        } elseif (isset($this->options[CURLOPT_READFUNCTION])) {
            $readCallback = $this->options[CURLOPT_READFUNCTION];
        }
        return $realCallback;
    }

    protected function send($url, $options) {
        if ($this->temporaryOptions !== null) {
            foreach ($this->temporaryOptions as $name => $value) {
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
                        continue;
                    }
                    if (self::$isOldCurl === false) {
                        curl_setopt($this->handle, $name, null);
                        continue;
                    }
                    if ($name === CURLOPT_FILE || CURLOPT_WRITEHEADER) {
                        curl_setopt(
                            $this->handle, $name, $this->getStdStream()
                        );
                        continue;
                    } elseif ($name === CURLOPT_STDERR) {
                        curl_setopt(
                            $this->handle, $name, $this->getStdStream(true)
                        );
                        continue;
                    } elseif ($name === CURLOPT_INFILE) {
                        continue;
                    }
                    curl_setopt($this->handle, $name, null);
                }
            }
        }
        if ($options !== null) {
            curl_setopt_array($this->handle, $options);
        }
        if (self::$isOldCurl) {
            $tmp = array();
            if (array_key_exists($options, CURLOPT_INFILE)) {
                $tmp[CURLOPT_INFILE] = $options[CURLOPT_INFILE];
            } elseif (array_key_exists($this->temporaryOptions, CURLOPT_INFILE)
            ) {
                if (isset($this->options[CURLOPT_INFILE])) {
                    $tmp[CURLOPT_INFILE] = $this->options[CURLOPT_INFILE];
                } else {
                    $tmp[CURLOPT_INFILE] = null;
                }
            } elseif (array_key_exists($options, CURLOPT_READFUNCTION)) {
                $tmp[CURLOPT_READFUNCTION] = $options[CURLOPT_READFUNCTION];
            } elseif (array_key_exists(
                $this->temporaryOptions, CURLOPT_READFUNCTION
            )) {
                if (isset($this->options[CURLOPT_READFUNCTION])) {
                    $tmp[CURLOPT_READFUNCTION] =
                        $this->options[CURLOPT_READFUNCTION];
                } else {
                    $tmp[CURLOPT_READFUNCTION] = null;
                }
            }
            $this->temporaryOptions = $options;
            $this->processDirtyInFileOption($tmp);
        } else {
            $this->temporaryOptions = $options;
        }
        curl_exec($this->handler);
    }

    private function getStdSteam($isError = false) {
        if (PHP_SAPI === 'cli' ) {
            if ($isError) {
                return STDERR;
            }
            return STDOUT;
        }
        if ($this->stdSteams === null) {
            $this->stdSteams = array();
        }
        if ($isError) {
            if (isset($this->stdSteams['error']) === false) {
                $this->stdSteams['error'] = fopen('php://stderr', 'w');
            }
            return $this->stdSteams['error'];
        }
        if (isset($this->stdSteams['output']) === false) {
            $this->stdSteams['output'] = fopen('php://output', 'w');
        }
        return $this->stdSteams['output'];
    }

    public funciton getInfo($name = 0) {
        return curl_getinfo($this->handle, $name);
    }

    public function pause($bitmask) {
        //php 5.5
        $result = curl_pause($this->handle, $bitmast);
        if ($result !== CURLE_OK) {
            throw new Exception;
        }
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
        $this->handle = curl_copy_handle(self::$handle);
    }

    public function get($url, $options) {
        curl_setopt($this->handler, CURLOPT_HTTPGET, true);
        self::send($url);
    }

    public static function sendAll($handlers) {
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
