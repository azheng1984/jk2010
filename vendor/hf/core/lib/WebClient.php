<?php
namespace Hyperframework;

use Exception;

class WebClient {
    private static $isOldCurl;
    private static $stdStreams;
    private static $multiHandle;
    private static $multiRequests;
    private static $multiRequestOptions;
    private static $multiProcessingRequests;
    private static $multiGetRequestCallback;
    private $handle;
    private $options = array();
    private $temporaryOptions;
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

    private static function addMultiRequest() {
        $request = null;
        if (self::$multiRequests !== null) {
            $key = key(self::$multiRequests);
            if ($key !== null) {
                $request = self::$multiRequests[$key];
                if ($request === null) {
                    throw new Exception;
                }
                unset(self::$multiRequests[$key]);
            } else {
                self::$multiRequests = null;
            }
        } elseif (self::$multiGetRequestCallback !== null) {
            $request = call_user_func(self::multiGetRequestCallback);
        }
        if ($request == false) {
            return false;
        }
        if (is_string($request)) {
            $request = array('url' => $request);
        }
        if (isset($request['client']) === false) {
            $request['client'] = new WebClient;
        } else {
            if ($request['client'] instanceof WebClient === false) {
                throw new Exception;
            }
        }
        $client = $request['client'];
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
        if (isset($request['options'])) {
            $options = $request['options'];
            if (self::$multiRequestOptions !== null) {
                $options += self::$multiRequestOptions;
            }
        } else {
            $options = self::$multiRequestOptions;
        }
        $client->prepare($method, $request['url'], $options);
        self::$multiProcessingRequests[intval($client->handle)] = $request;
        curl_multi_add_handle(self::$multiHandle, $client->handle);
    }

    public static function sendAll(
        $requests,
        $onCompleteCallback,
        $requestOptions = null,
        $multiOptions = null
    ) {
        if ($requests !== null && count($requests) !== 0) {
            self::$multiRequests = $requests;
        } else {
            self::$multiRequests = null;
        }
        self::$multiGetRequestCallback = null;
        self::$multiProcessingRequests = array();
        $maxHandles = 100;
        $getRequestCallback = null;
        $selectTimeout = 1;
        $handleCount = 0;
        if (count($requests) === 0 && $getRequestCallback === null) {
            return;
        }
        if (self::$multiHandle === null) {
            self::$multiHandle = curl_multi_init();
        }
        $hasRequest = true;
        $processingRequests = array();
        for ($index = 0; $index < $maxHandles; ++$index) {
            $hasRequest = self::addMultiRequest() !== false;
            if ($hasRequest === false) {
                break;
            }
        }
        $isRunning = null;
        do {
            do {
                $status = curl_multi_exec(self::$multiHandle, $isRunning);
            } while ($status === CURLM_CALL_MULTI_PERFORM);
            if ($status !== CURLM_OK) {
                $message = '';
                if (self::$isOldCurl === false) {
                    $message = curl_multi_strerror($status);
                }
                throw new CurlMultiException($message, $status);
            }
            while ($info = curl_multi_info_read(self::$multiHandle)) {
                $handleId = intval($info['handle']);
                $request = self::$multiProcessingRequests[$handleId];
                $response = array('curl_code' => $info['result']);
                if ($request['client']->getOption(CURLOPT_RETURNTRANSFER)) {
                    $response['content'] =
                        curl_multi_getcontent($info['handle']);
                }
                if ($onCompleteCallback !== null) {
                    call_user_func(
                        $onCompleteCallback, $request, $response
                    );
                }
                unset(self::$multiProcessingRequests[$handleId]);
                if ($hasRequest) {
                    $hasRequest = self::addMultiRequest() !== false;
                }
                curl_multi_remove_handle(self::$multiHandle, $info['handle']);
            }
            if ($isRunning) {
                curl_multi_select(self::$multiHandle, $selectTimeout);
            }
        } while ($isRunning);
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

    protected function getDefaultOptions() {
        return array(
            CURLOPT_TIMEOUT => 30,
            CURLOPT_CONNECTTIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => 1,
            CURLOPT_MAXREDIRS => 100,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_ENCODING => '',
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
        $result = curl_exec($this->handle);
        if ($result === false) {
            throw new CurlException(curl_error(), curl_errno());
        }
        return $result;
    }

    protected function prepare($method, $url, $options) {
        if ($this->temporaryOptions !== null) {
            foreach ($this->temporaryOptions as $name => $value) {
                if ($options !== null && array_key_exists($name, $options)) {
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
            if (array_key_exists(CURLOPT_INFILE, $options)
                || array_key_exists(CURLOPT_INFILE, $this->options)
            ) {
                $this->isInFileOptionDirty = false;
                $readCallback = $this->$this->getOption(CURLOPT_READFUNCTION);
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
        $readCallback = $this->getOption(CURLOPT_READFUNCTION);
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

    private function getOption($name) {
        $result = null;
        if ($this->temporaryOptions !== null && array_key_exists(
            $name, $this->temporaryOptions
        )) {
            $result = $this->temporaryOptions[$name];
        } elseif (isset($this->options[$name])) {
            $result = $this->options[$name];
        }
        return $result;
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

    public function getInfo($name = null) {
        if ($name === null) {
            return curl_getinfo($this->handle);
        }
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
        $this->options = array();
        $this->temporaryOptions = null;
        $this->isInFileOptionDirty = null;
    }

    public static function resetStaticProperties() {
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
