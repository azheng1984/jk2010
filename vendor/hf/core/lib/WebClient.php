<?php
namespace Hyperframework;

use Exception;

class WebClient {
    private static $isOldCurl;
    private static $stdStreams;
    private static $multiHandle;
    private static $multiOptions = array();
    private static $multiPendingRequests;
    private static $multiProcessingRequests;
    private static $multiRequestOptions;
    private static $multiGetRequestCallback;
    private static $defaultOptionValues;
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
            $request = array('url' => $request);
        } elseif (isset($request['url']) === false) {
            throw new Exception;
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
                    if ($name === CURLMOPT_MAXCONNECTS) {
                        self::setMultiOption($name, 10);
                    } else {
                        self::setMultiOption($name, null);
                    }
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

    public static function getMultiOptions($name, $default = null) {
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

    public static function setMultiOption($name, $value) {
        self::setMultiOpitons(array($name, $value));
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
            if (array_key_exists($name, $this->options)) {
                unset($this->options[$name]);
            }
            $this->options[$name] = $value;
            if (self::$isOldCurl && $this->temporaryOptions !== null) {
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

    private function resetOption($name) {
        if ($name === CURLOPT_HTTPHEADER
            || $name === CURLOPT_POSTQUOTE
            || $name === CURLOPT_QUOTE
            || (defined('CURLOPT_HTTP200ALIASES')
                && $name === CURLOPT_HTTP200ALIASES)
        ) {
            curl_setopt($handle, $name, array());
            return;
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
        }
        if (self::$defaultOptionValues === null) {
            self::$defaultOptionValues = array();
        }
        elseif ($name === CURLOPT_DNS_USE_GLOBAL_CACHE
            || $name === CURLOPT_NOPROGRESS
            || $name === CURLOPT_SSL_VERIFYPEER
        ) {
            curl_setopt($this->handle, $name, true);
        } elseif ($name === CURL_MAX_WRITE_SIZE) {
            curl_setopt($this->handle, $name, 16384);
        } elseif ($name === CURLOPT_CONNECTTIMEOUT) {
            curl_setopt($this->handle, $name, 300);
        } elseif ($name === CURLOPT_CONNECTTIMEOUT_MS) {
            curl_setopt($this->handle, $name, 300000);
        } elseif ($name === CURLOPT_DNS_CACHE_TIMEOUT) {
            curl_setopt($this->handle, $name, 120);
        } elseif ($name === CURLOPT_FTPSSLAUTH) {
            curl_setopt($this->handle, $name, CURLFTPAUTH_DEFAULT);//0
        } elseif ($name === CURLOPT_HTTP_VERSION) {
            curl_setopt($this->handle, $name, CURL_HTTP_VERSION_NONE);//0
        } elseif ($name === CURLOPT_HTTPAUTH || $name === CURLOPT_PROXYAUTH) {
            curl_setopt($this->handle, $name, CURLAUTH_BASIC);//1
        } elseif ($name === CURLOPT_INFILESIZE) {
            curl_setopt($this->handle, $name, -1);
        } elseif ($name === CURLOPT_MAXCONNECTS) {
            curl_setopt($this->handle, $name, 5);
        } elseif ($name === CURLOPT_MAXREDIRS) {
            curl_setopt($this->handle, $name, -1);
        } elseif ($name === CURLOPT_PROTOCOLS || $name === CURLOPT_REDIR_PROTOCOLS) {
            curl_setopt($this->handle, $name, CURLPROTO_ALL);//-1
        } elseif ($name === CURLOPT_PROXYTYPE) {
            curl_setopt($this->handle, $name, CURLPROXY_HTTP);//0
        } elseif ($name === CURLOPT_SSL_VERIFYHOST) {
            curl_setopt($this->handle, $name, 2);
        } elseif ($name === CURLOPT_SSLVERSION) {
            curl_setopt($this->handle, $name, CURL_SSLVERSION_DEFAULT);//0
        } elseif ($name === CURLOPT_TIMECONDITION) {
            curl_setopt($this->handle, $name, CURL_TIMECOND_IFMODSINCE);//1
        } elseif ($name === CURLOPT_SSH_AUTH_TYPES) {
            curl_setopt($this->handle, $name, CURLSSH_AUTH_ANY);//-1
        } elseif ($name === CURLOPT_IPRESOLVE) {
            curl_setopt($this->handle, $name, CURL_IPRESOLVE_WHATEVER);//0
        } elseif ($name === CURLOPT_SSLCERTTYPE || $name === CURLOPT_SSLKEYTYPE) {
            curl_setopt($this->handle, $name, 'PEM');
        } elseif ($name === CURLOPT_SSLENGINE_DEFAULT) {
            curl_setopt($this->handle, $name, CURLE_SSL_ENGINE_SETFAILED);//54
        } else {
            curl_setopt($this->handle, $name, null);
        }
    }

    protected function prepare($method, $url, $options) {
        if ($this->temporaryOptions !== null) {
            if (self::$isOldCurl === false) {
                curl_reset($this->handle);
                curl_setopt_array($this->handle, $this->options);
            } else {
                foreach ($this->temporaryOptions as $name => $value) {
                    if ($options !== null && array_key_exists($name, $options)) {
                        continue;
                    }
                    if (isset($this->options[$name])) {
                        curl_setopt($handle, $name, $this->options[$name]);
                    } else {
                        self::resetOption($name);
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
        if ($this->temporaryOptions !== null
            && array_key_exists($name, $this->temporaryOptions)
        ) {
            return $this->temporaryOptions[$name];
        } elseif (isset($this->options[$name])) {
            return $this->options[$name];
        }
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
        if ($this->handle === null) {
            throw new Exception;
        }
        if (self::$isOldCurl === false) {
            $this->temporaryOptions = null;
            curl_reset($this->handle);
        } else {
            foreach ($this->options as $name => $value) {
                $this->resetOptions($name);
                if ($this->temporaryOptions !== null) {
                    unset($this->temporaryOptions[$name]);
                }
            }
        }
        $this->options = array();
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
