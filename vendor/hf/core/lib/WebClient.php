<?php
namespace Hyperframework;

use Exception;

class WebClient {
    private static $isOldCurl;
    private static $multiHandle;
    private static $multiOptions = array();
    private static $multiTemporaryOptions;
    private static $multiPendingRequests;
    private static $multiProcessingRequests;
    private static $multiRequestOptions;
    private static $multiGetRequestCallback;
    private static $oldCurlMultiHandle;
    private $handle;
    private $options = array();
    private $temporaryOptions;

    public function __construct($options = null) {
        if (self::$isOldCurl === null) {
            self::$isOldCurl = version_compare(phpversion(), '5.5.0', '<');
        }
        $this->handle = curl_init();
        $defaultOptions = $this->getDefaultOptions();
        if ($defaultOptions === null) {
            $defaultOptions = array();
        }
        if ($options !== null) {
            foreach ($options as $name => $value) {
                $defaltOptions[$name] = $value;
            }
        }
        if (count($defaultOptions) !== 0) {
            $this->setOptions($defaultOptions);
        }
    }

    private static function addMultiRequest() {
        $request = null;
        if (self::$multiPendingRequests !== null) {
            $key = key(self::$multiPendingRequests);
            if ($key !== null) {
                $request = self::$multiPendingRequests[$key];
                if ($request === null) {
                    throw new Exception;
                }
                unset(self::$multiPendingRequests[$key]);
            } else {
                self::$multiPendingRequests = null;
            }
        } elseif (self::$multiGetRequestCallback !== null) {
            $request = call_user_func(self::multiGetRequestCallback);
        }
        if ($request == false) {
            return false;
        }
        if (is_string($request)) {
            $request = array(CURLOPT_URL => $request);
        }
        if (isset($request['client']) === false) {
            $request['client'] = new WebClient;
        } else {
            if ($request['client'] instanceof WebClient === false) {
                throw new Exception;
            }
        }
        $handleId = intval($request['client']->handle);
        self::$multiProcessingRequests[$handleId] = $request;
        if (self::$multiRequestOptions !== null) {
            foreach (self::$multiRequestOptions as $name => $value) {
                if (isset($request[$name]) === false) {
                    $request[$name] = $value;
                }
            }
        }
        $options = $request;
        unset($options['client']);
        $request['client']->prepare($options);
        curl_multi_add_handle(self::$multiHandle, $request['client']->handle);
    }

    private static function getMultiOptionDefaultValue($name) {
        if ($name === CURLMOPT_MAXCONNECTS) {
            return 10;
        }
        return null;
    }

    public static function sendAll(
        $requests,
        $onCompleteCallback,
        $requestOptions = null,
        $multiOptions = null
    ) {
        if ($requests !== null && count($requests) !== 0) {
            self::$multiPendingRequests = $requests;
        } else {
            self::$multiPendingRequests = null;
        }
        self::$multiRequestOptions = $requestOptions;
        self::$multiProcessingRequests = array();
        self::$multiGetRequestCallback = self::getMultiOptions(
            'get_request_callback'
        );
        if (self::$multiPendingRequests === null
            && self::$multiGetRequestCallback === null
        ) {
            return;
        }
        if (self::$multiHandle === null) {
            self::$multiHandle = curl_multi_init();
            if (self::$multiOptions !== null) {
                self::setMultiOptions(self::$multiOptions);
            }
        } elseif (self::$multiTemporaryOptions !== null) {
            foreach (self::$multiTemporaryOptions as $name => $value) {
                if (is_int($name) === false) {
                    continue;
                }
                if (isset(self::$multiOptions[$name])) {
                    self::setMultiOption($name, self::$multiOptions[$name]);
                } else {
                    self::setMultiOption(
                        $name, self::getMultiOptionDefaultValue($name)
                    );
                }
            }
        }
        if ($multiOptions !== null) {
            foreach ($multiOptions as $name => $value) {
                if (is_int($name)) {
                    if (self::$isOldCurl) {
                        throw new Exception;
                    }
                    curl_multi_setopt(self::$multiHandle, $name, $value);
                }
            }
        }
        self::$multiTemporaryOptions = $multiOptions;
        $hasPendingRequest = true;
        $maxHandles = self::getMultiOptions('max_handles', 100);
        if ($maxHandles < 1) {
            throw new Exception;
        }
        for ($index = 0; $index < $maxHandles; ++$index) {
            $hasPendingRequest = self::addMultiRequest() !== false;
            if ($hasPendingRequest === false) {
                break;
            }
        }
        $selectTimeout = self::getMultiOptions('select_timeout', 1);
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
                curl_multi_close(self::$multiHandle);
                self::$multiHandle = null;
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
                if ($hasPendingRequest) {
                    $hasPendingRequest = self::addMultiRequest() !== false;
                }
                curl_multi_remove_handle(self::$multiHandle, $info['handle']);
            }
            if ($isRunning) {
                $tmp = curl_multi_select(self::$multiHandle, $selectTimeout);
                //https://bugs.php.net/bug.php?id=61141
                if ($tmp === -1) {
                    usleep(100);
                };
            }
        } while ($hasPendingRequest || $isRunning);
    }

    private function getOption($name) {
        if ($this->temporaryOptions !== null
            && array_key_exists($name, $this->temporaryOptions)
        ) {
            return $this->temporaryOptions[$name];
        } elseif (isset($this->options[$name])) {
            return $this->options[$name];
        }
    }

    public static function setMultiOptions($options) {
        foreach ($options as $name => $value) {
            self::$multiOptions[$name] = $value;
            if (self::$multiHandle !== null && is_int($name)) {
                if (self::$isOldCurl) {
                    throw new Exception;
                }
                curl_multi_setopt(self::$multiHandle, $name, $value);
                if (self::$multiTemporaryOptions !== null) {
                    unset(self::$multiTemporaryOptions[$name]);
                }
            }
        }
    }

    public static function setMultiOption($name, $value) {
        self::setMultiOpitons(array($name, $value));
    }

    private static function getMultiOptions($name, $default = null) {
        $result = null;
        if (self::$multiTemporaryOptions !== null
            && array_key_exists($name, self::$multiTemporaryOptions)
        ) {
            $result = self::$multiTemporaryOptions[$name];
        } elseif (isset(self::$multiOptions[$name])) {
            $result = self::$multiOptions[$name];
        }
        if ($result === null) {
            return $default;
        }
        return $result;
    }

    public static function closeMultiHandle() {
        curl_multi_close(self::$multiHandle);
        self::$multiHandle = null;
        self::$multiOptions = array();
        self::$multiTemporaryOptions = null;
    }

    public static function resetMultiHandle() {
        if (self::$multiTemporaryOptions !== null) {
            foreach (self::$multiTemporaryOptions as $name => $value) {
                if (isset(self::$multiOptions[$name]) === false) {
                    if (is_int($name)) {
                        curl_multi_setopt(
                            self::$multiHandle,
                            $name,
                            self::getMultiOptionDefaultValue($name)
                        );
                    }
                }
            }
        }
        self::$multiTemporaryOptions = null;
        foreach (self::$multiOptions as $name => $value) {
            if (is_int($name)) {
                curl_multi_setopt(
                    self::$multiHandle,
                    $name,
                    self::getMultiOptionDefaultValue($name)
                );
            }
        }
        self::$multiOptions = array();
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
            $this->options[$name] = $value;
        }
    }

    public function setOption($name, $value) {
        $this->setOptions(array($name => $value));
    }

    private function sendHttp($method, $url, $options) {
        if ($options === null) {
            $options = array();
        }
        $options[CURLOPT_CUSTOMREQUEST] = $method;
        $options[CURLOPT_URL] = $url;
        self::send($options);
    }

    public function send($options = null) {
        $this->prepare($options);
        if (self::$isOldCurl === false) {
            $result = curl_exec($this->handle);
            if ($result === false) {
                throw new CurlException(
                    curl_error($this->handle), curl_errno($this->handle)
                );
            }
            return $result;
        }
        if (self::$oldCurlMultiHandle === null) {
            self::$oldCurlMultiHandle = curl_multi_init();
        }
        curl_multi_add_handle(self::$oldCurlMultiHandle, $this->handle);
        $isRunning = null;
        do {
            do {
                $status = curl_multi_exec(
                    self::$oldCurlMultiHandle, $isRunning
                );
            } while ($status === CURLM_CALL_MULTI_PERFORM);
            if ($status !== CURLM_OK) {
                $message = '';
                if (self::$isOldCurl === false) {
                    $message = curl_multi_strerror($status);
                }
                curl_multi_close(self::$multiHandle);
                self::$multiHandle = null;
                throw new CurlMultiException($message, $status);
            }
            if ($isRunning && curl_multi_select(self::$isRunning) === -1) {
                //https://bugs.php.net/bug.php?id=61141
                usleep(100);
            }
        } while ($running);
        curl_multi_remove_handle(self::$oldCurlMultiHandle, $this->handle);
    }

    protected function prepare($options) {
        if ($this->temporaryOptions !== null) {
            if (self::$isOldCurl === false) {
                curl_reset($this->handle);
            } else {
                curl_close($this->handle);
                $this->handle = curl_init();
            }
        }
        curl_setopt_array($this->handle, $this->options);
        if ($options !== null && count($options) !== 0) {
            curl_setopt_array($this->handle, $options);
            $this->temporaryOptions = $options;
        } else {
            $this->temporaryOptions = null;
        }
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
        if (self::$isOldCurl === false) {
            curl_reset($this->handle);
        } else {
            curl_close($this->handle);
            $this->hanlde = curl_init();
        }
        $this->temporaryOptions = null;
        $this->options = array();
    }

    public function close() {
        curl_close($this->handle);
        $this->handle = null;
        if (self::$isOldCurl) {
            if (self::$oldCurlMultiHandle !== null) {
                curl_multi_close(self::$oldCurlMultiHandle);
            }
        }
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
        return self::sendHttp('HEAD', $url, $options);
    }

    public function get($url, $options = null) {
        return self::sendHttp('GET', $url, $options);
    }

    public function post($url, $fields = null, $options = null) {
        return self::sendHttp('POST', $url, $options);
    }

    public function patch($url, $fields = null, $options = null) {
        return self::sendHttp('PATCH', $url, $options);
    }

    public function put($url, $fields = null, $options = null) {
        return self::sendHttp('PUT', $url, $options);
    }

    public function delete($url, $options = null) {
        return self::sendHttp('DELETE', $url, $options);
    }

    public function options($url, $options = null) {
        return self::sendHttp('OPTIONS', $url, $options);
    }

    public function trace($url, $options = null) {
        return self::sendHttp('TRACE', $url, $options);
    }
}
