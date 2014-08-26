<?php
namespace Hyperframework;

use Exception;

class WebClient {
    private static $isOldCurl;
    private $handle;
    private static $multiHandle;
    private $options = array();
    private $temporaryOptions;
    private $stdStreams;
    private $isInFileOptionDirty;

    public function __construct($options = null) {
        if (self::$isOldCurl === null) {
            self::$isOldCurl = version_compare(phpversion(), '5.5.0', '<');
        }
        $this->handle = curl_init();
        $defaultOptions = $this->getDefaultOptions();
        if ($defaultOptions !== null) {
            $this->setOptions($defaultOptions);
        }
        if ($options !== null) {
            $this->setOptions($defaultOptions);
        }
    }

    public static function sendAll($requests, $multiOptions = null) {
        if (count($request) === 0) {
            return;
        }
        if (self::$multiHandle === null) {
            self::$multiHandle = curl_multi_init();
        }
        foreach ($requests as &$request) {
            if (is_string($request)) {
                $request = array('url' => $request);
            }
            $client = null;
            if (isset($request['client']) === false) {
                $client = new WebClient;
                $request['client'] = $client;
            } else {
                $client = $request['client'];
                if ($client instanceof WebClient === false) {
                    throw new Exception;
                }
            }
            $method = null;
            if (isset($request['method']) === false) {
                $method = 'GET';
            } else {
                $method = $request['method'];
            }
            if (isset($request['url']) === false) {
                throw new Exception;
            }
            $options = null;
            if (isset($request['options']) === false) {
                $options = $request['options'];
            }
            $client->prepare(
                $request['method'], $request['url'], $request['options']
            );
            curl_multi_add_handle(self::$multiHandle, $client->handle);
        }
        foreach ($requests as $request) {
            $client = $request['client'];
            curl_multi_remove_handle(self::$multiHandle, $client->handle);
        }
    }

    public static function setMultiOptions($options) {
    }

    public static function setMultiOption($name, $value) {
    }

    public static function closeMultiHandle() {
        if (self::$multiHandle === null) {
            throw new Exception;
        }
        curl_multi_close(self::$multiHandle);
        self::$multiHandle = null;
    }

    public static function getMultiInfo() {
        return curl_multi_info_read(self::$multiHandle);
    }

    protected function getDefaultOptions() {
        return array(
            CURLOPT_TIMEOUT => 30,
            CURLOPT_ENCODING => '',
            CURLOPT_FOLLOWLOCATION => 1,
            CURLOPT_RETURNTRANSFER => 1,
        );
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
    }

    public function setOption($name, $value) {
        $this->setOptions(array($name => $value));
    }

    protected function send($method, $url, $options) {
        $this->prepare($method, $url, $options);
        return curl_exec($this->handle);
    }

    protected function prepare($method, $url, $options) {
        if ($this->temporaryOptions !== null) {
            foreach ($this->temporaryOptions as $name => $value) {
                if ($options !== null && array_key_exists($options, $name)) {
                    continue;
                }
                if (isset($this->options[$name])) {
                    curl_setopt($handle, $name, $this->options[$name]);
                } else {
                    if ($name === CURLOPT_HTTPHEADER
                        || $name === CURLOPT_POSTQUOTE
                        || $name === CURLOPT_QUOTE
                        || (defined('CURLOPT_HTTP200ALIASES')
                            && $name === CURLOPT_HTTP200ALIASES)
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
                    } elseif ($name === CURLOPT_STDERR) {
                        curl_setopt(
                            $this->handle, $name, $this->getStdStream(true)
                        );
                    } elseif ($name === CURLOPT_INFILE) {
                        $this->isInFileOptionDirty = true;
                    } else {
                        curl_setopt($this->handle, $name, null);
                    }
                }
            }
        }
        if ($options !== null) {
            curl_setopt_array($this->handle, $options);
        }
        $this->temporaryOptions = $options;
        if (self::$isOldCurl && $this->isInFileOptionDirty) {
            if (array_key_exists($options, CURLOPT_INFILE)
                || array_key_exists($this->options, CURLOPT_INFILE)
            ) {
                $this->isInFileOptionDirty = false;
                $readCallback = $this->getReadCallback();
                if ($readCallback === null) {
                    curl_setopt($this->handle, CURLOPT_READFUNCTION, null);
                } else {
                    curl_setopt(
                        $this->handle, CURLOPT_READFUNCTION, $readCallback
                    );
                }
            } else {
                $this->addReadWrapper();
            }
        }
        curl_setopt($this->handle, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($this->handle, CURLOPT_URL, $url);
    }

    private function addReadWrapper() {
        $callback = null;
        $readCallback = $this->getReadCallback();
        if ($readCallback !== null) {
            $callback = function($handle, $dirtyHandle, $maxLength)
                use ($readCallback)
            {
                return call_user_func($readCallback, $handle, null, $maxLength);
            };
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

    private function getStdStream($isError = false) {
        if (PHP_SAPI === 'cli' ) {
            if ($isError) {
                return STDERR;
            }
            return STDOUT;
        }
        if ($this->stdStreams === null) {
            $this->stdStreams = array();
        }
        if ($isError) {
            if (isset($this->stdStreams['error']) === false) {
                $this->stdStreams['error'] = fopen('php://stderr', 'w');
            }
            return $this->stdStreams['error'];
        }
        if (isset($this->stdStreams['output']) === false) {
            $this->stdStreams['output'] = fopen('php://output', 'w');
        }
        return $this->stdStreams['output'];
    }

    public function getInfo($name = 0) {
        return curl_getinfo($this->handle, $name);
    }

    public function pause($bitmask) {
        if (self::$isOldCurl) {
            throw new Exception;
        }
        $result = curl_pause($this->handle, $bitmast);
        if ($result !== CURLE_OK) {
            throw new Exception;
        }
    }

    public function reset() {
        if (self::$isOldCurl) {
            throw new Exception;
        }
        curl_reset($this->handle);
    }

    public function close() {
        curl_close($this->handle);
        $this->handle = null;
    }

    public function __destruct() {
        if ($this->handle !== null) {
            $this->close();
        }
    }

    public function __clone() {
        $this->handle = curl_copy_handle(self::$handle);
    }

    public function head($url, $options = null) {
        return self::send('HEAD', $url, $options);
    }

    public function get($url, $options = null) {
        return self::send('GET', $url, $options);
    }

    public function post($url, $options = null) {
        return self::send('POST', $url, $options);
    }

    public function patch($url, $options = null) {
        return self::send('PATCH', $url, $options);
    }

    public function put($url, $options = null) {
        return self::send('PUT', $url, $options);
    }

    public function delete($url, $options = null) {
        return self::send('DELETE', $url, $options);
    }

    public function options($url, $options = null) {
        return self::send('OPTIONS', $url, $options);
    }

    public function trace($url, $options = null) {
        return self::send('TRACE', $url, $options);
    }
}
