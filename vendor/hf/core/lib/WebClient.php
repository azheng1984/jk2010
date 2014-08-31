<?php
namespace Hyperframework;

use Exception;

class WebClient {
    private static $multiHandle;
    private static $multiOptions = array();
    private static $multiTemporaryOptions;
    private static $multiPendingRequests;
    private static $multiProcessingRequests;
    private static $multiRequestOptions;
    private static $multiGetRequestCallback;
    private static $isOldCurl;
    private static $oldCurlMultiHandle;
    private $handle;
    private $headers = array();
    private $curlOptions = array();
    private $ignoredCurlOptions;
    private $temporaryCurlOptions;
    private $isCurlOptionChanged;
    private $rawResponseHeaders;
    private $responseHeaders;

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
                $defaultOptions[$name] = $value;
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
        $handleId = (int)$request['client']->handle;
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
        $onCompleteCallback = null,
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
                $handleId = (int)$info['handle'];
                if ($onCompleteCallback !== null) {
                    $request = self::$multiProcessingRequests[$handleId];
                    $response = array('curl_code' => $info['result']);
                    if ($info['result'] !== CURLE_OK) {
                        $response['error'] =
                            curl_error($info['handle']);
                    }
                    if ($request['client']->getCurlOption(CURLOPT_RETURNTRANSFER)) {
                        $response['content'] =
                            curl_multi_getcontent($info['handle']);
                    }
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

    private function getCurlOption($name) {
        if ($this->temporaryCurlOptions !== null
            && array_key_exists($name, $this->temporaryCurlOptions)
        ) {
            return $this->temporaryCurlOptions[$name];
        } elseif (isset($this->curlOptions[$name])) {
            return $this->curlOptions[$name];
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
            CURLOPT_TIMEOUT => 3,
            CURLOPT_CONNECTTIMEOUT => 3,
            CURLOPT_FOLLOWLOCATION => 1,
            CURLOPT_MAXREDIRS => 100,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_ENCODING => '',
        );
    }

    public function setOptions($options) {
        if (isset($options['headers'])) {
            $this->setHeaders($options['headers']);
            unset($options['headers']);
        }
        $this->isCurlOptionChanged = true;
        foreach ($options as $name => $value) {
            if (is_int($name)) {
                if ($name === CURLOPT_HEADERFUNCTION 
                    || $name === CURLOPT_WRITEFUNCTION
                ) {
                    $client = $this;
                    $value = function($handle, $data) use ($client, $value) {
                        return call_user_func($value, $client, $data);
                    };
                } elseif ($name === CURLOPT_READFUNCTION
                    || (defined('CURLOPT_PASSWDFUNCTION')
                        && $name === CURLOPT_PASSWDFUNCTION)
                ) {
                    $client = $this;
                    $value = function($handle, $arg1, $arg2)
                        use ($client, $value)
                    {
                        return call_user_func($value, $client, $arg1, $arg2);
                    };
                } elseif ($name === CURLOPT_PROGRESSFUNCTION) {
                    $client = $this;
                    $value = function($handle, $arg1, $arg2, $arg3, $arg4)
                        use ($client, $value)
                    {
                        return call_user_func(
                            $value, $client, $arg1, $arg2, $arg3, $arg4
                        );
                    };
                }
                $this->curlOptions[$name] = $value;
            } else {
                throw new Exception;
            }
        }
    }

    public function removeOption($name) {
        if ($name === 'headers') {
            $this->headers = array();
            return;
        }
        $this->isCurlOptionChanged = true;
        unset($this->curlOptions[$name]);
    }

    public function setOption($name, $value) {
        $this->setOptions(array($name => $value));
    }

    private function sendHttp($method, $url, $data, $headers, $options) {
        if ($options === null) {
            $options = array();
        }
        if ($headers !== null && count($headers) !== 0) {
            if (isset($options['headers']) && count($options['headers']) !== 0) {
                foreach ($headers as $key => $value) {
                    if (is_int($key)) {
                        $options['headers'][] = $key;
                    } else {
                        if (array_key_exists($key, $options['headers'])) {
                            unset($options['headers'][$key]);
                        }
                    }
                    $options['headers'][$key] = $value;
                }
            } else {
                $options['headers'] = $headers;
            }
        }
        if ($data !== null) {
            $options['data'] = $data;
        }
        $options[CURLOPT_URL] = $url;
        $options[CURLOPT_CUSTOMREQUEST] = $method;
        return self::send($options);
    }

    private function setTemporaryHeaders($headers, &$options) {
        if ($headers === null || count($headers) === 0) {
            return;
        }
        if ($options === null) {
            $options = array();
        }
        if (isset($options['headers']) === false) {
            $options['headers'] = array();
        }
        foreach ($headers as $key => $value) {
            if (is_int($key)) {
                $tmp = explode(':', $value, 2);
                $key = $tmp[0];
                $value = null;
                if (count($tmp) === 2) {
                    $value = $tmp[1];
                }
            }
            if ($key == '') {
                throw new Exception;
            }
            $options['headers'][$key] = $value;
        }
    }

    public function setHeaders($headers) {
        if ($this->headers === null) {
            $this->headers = array();
        }
        foreach ($headers as $key => $value) {
            if (is_int($key)) {
                $tmp = explode(':', $value, 2);
                $key = $tmp[0];
                $value = null;
                if (count($tmp) === 2) {
                    $value = $tmp[1];
                }
            }
            if ($key == '') {
                throw new Exception;
            }
            $this->headers[$key] = $value;
        }
    }

    public function resetHeaders() {
        $this->headers = null;
    }

    private function setData($data, &$options) {
        curl_setopt(CURLOPT_POST, true);
        if (is_string($data)) {
            $options[CURLOPT_POSTFIELDS] = $data;
            return;
        }
        if (count($data) === 1) {
            $data = array('type' => key($data), 'content' => reset($data));
        }
        if ($data['type'] === 'application/x-www-form-urlencoded') {
            if (is_array($data)) {
                $content = null;
                foreach ($data as $key => $value) {
                    if ($content !== null) {
                        $content .= '&';
                    }
                    $content .= urlencode($key) . '=' . urlencode($value);
                }
                $options[CURLOPT_POSTFIELDS] = $content;
            } else {
                $options[CURLOPT_POSTFIELDS] = (string)$data['content'];
            }
        } elseif ($data['type'] !== 'multipart/form-data') {
            $this->setTemporaryHeaders(
                array('Content-Type' => $data['type']), $options
            );
            if (isset($data['content'])) {
                $options[CURLOPT_POSTFIELDS] = (string)$data['content'];
            } elseif (isset($data['file'])) {
                $size = filesize($data['file']);
                if ($size === false) {
                    throw new Exception;
                }
                $this->setTemporaryHeader(array('Content-Length' => $size), $options);
                if ($size !== 0) {
                    if (isset($this->curlOptions[CURLOPT_POSTFIELDS])) {
                        if (isset($options['ignored_curl_optoins']) === false) {
                            $options['ignored_curl_options'] = array();
                        }
                        $options['ignored_curl_options'][] = CURLOPT_POSTFIELDS;
                    }
                    $options[CURLOPT_READFUNCTION] = self::getSendContentCallback($data);
                } else {
                    $options[CURLOPT_POSTFIELDS] = '';
                }
            }
        } else {
            if (isset($data['content']) === false
                || is_array($data['content']) === false
            ) {
                throw new Exception;
            }
            $isSafe = true;
            $canUsePostFields = true;
            foreach ($data['content'] as $key => $value) {
                if (is_array($value) === false) {
                    $value = (string)$value;
                    if (is_int($key)) {
                        throw new Exception;
                    }
                    if (strlen($value) !== 0 && $value[0] === '@') {
                        if (self::$isOldCurl) {
                            $canUsePostFields = false;
                            break;
                        }
                        $isSafe = false;
                    }
                } else {
                    if (isset($value['content'])) {
                        if (isset($value['type'])) {
                            $canUsePostFields = false;
                            break;
                        }
                        $value = (string)$value;
                        if (strlen($value) !== 0 && $value[0] === '@') {
                            if (self::$isOldCurl) {
                                $canUsePostFields = false;
                                break;
                            }
                            $isSafe = false;
                        }
                    } elseif (self::$isOldCurl) {
                        if (isset($value['type'])
                            && $value['type'] !== 'application/octet-stream'
                        ) {
                            $canUsePostFields = false;
                            break;
                        }
                    }
                }
            }
            if ($canUsePostFields) {
                if (self::$isOldCurl) {
                    if ($isSafe === false) {
                        $options[CURLOPT_SAFE_UPLOAD] = true;
                    }
                    foreach ($data as $key => &$value) {
                        if (is_array($value)) {
                            if (isset($value['content'])) {
                                $value = $value['content'];
                            } elseif (isset($value['file'])) {
                                throw new Exception;
                            }
                            $type = null;
                            if (isset($value['type'])) {
                                $type = $value['type'];
                            }
                            $fileName = basename($value['file']);
                            if (isset($value['file_name'])) {
                                $file = $value['file_name'];
                            }
                            $value = curl_file_create(
                                $value['file'], $type, $fileName
                            );
                        }
                    }
                } else {
                    foreach ($data as $key => &$value) {
                        if (is_array($value)) {
                            if (isset($value['content'])) {
                                $value = $value['content'];
                            } elseif (isset($value['file'])) {
                                $value = '@' . $value['file'];
                            }
                        }
                    }
                }
                $options[CURLOPT_POSTFIELDS] = $data;
                return;
            }
            if (isset($this->curlOptions[CURLOPT_POSTFIELDS])) {
                if (isset($options['ignored_optoins']) === false) {
                    $options['ignored_options'] = array();
                }
                $options['ignored_options'][] = CURLOPT_POSTFIELDS;
            }
            $boundary = '--BOUNDARY-' . sha1(uniqid(mt_rand(), true));
            $this->setTemporaryHeader(
                array('Content-Type' => 'multipart/form-data; boundary='
                    . $boundary
                ),
                $options
            );
            $options[CURLOPT_READFUNCTION] = self::getFormDataCallback($data, $boundary);
        }
    }

    private function getSendContentCallback($data) {
        $file = fopen($data['file']);
        if ($file === false) {
            throw new Exception;
        }
        return function($handle, $inFile, $maxLength) use (&$file) {
            if (feof($file)) {
                fclose($file);
                return '';
            }
            $result = fgets($file, $maxLength);
            if ($result === false) {
                throw Exception;
            }
            return $result;
        };
    }

    private function getSendFormDataCallback($data, $boundary) {
        foreach ($data as $key => &$value) {
            $header = $boundary . "\r\n";
            $type = null;
            $fileName = null;
            if (isset($value['content']) === false && isset($value['file'])) {
                $fileName = '"; filename="' . basename($value['file']) . '"';
                $type = 'application/octet-stream';
            }
            if (isset($value['type'])) {
                $type = $value['type'];
            }
            if ($type !== null) {
                $type = "\r\nContent-Type: " . $type;
            }
            $header .= 'Content-Disposition: form-data; name="' . $key
                . $fileName . $type . "\r\n";
            if (is_array($value) === false) {
                $value = array('content' => $value);
            }
            $value['header'] = $header;
        }
        $cache = null;
        $file = null;
        $isFirst = true;
        $isEnd = false;
        return function($handle, $inFile, $maxLength) use (
            &$data, &$cache, &$file, &$isFirst, &$isEnd
        ) {
            if ($isEnd) {
                return;
            }
            for (;;) {
                $cacheLength = strlen($cache);
                if ($cacheLength !== 0) {
                    if ($maxLength <= $cacheLength) {
                        $result = substr($cache, 0, $maxLength);
                        $cache = substr($cache, $maxLength);
                        return $result;
                    } else {
                        $result = $cache;
                        $cache = null;
                        return $result;
                    }
                }
                if ($file === null) {
                    if (count($data) === 0) {
                        $isEnd  = true;
                        return "\r\n" . $boundary . '--';
                    }
                    $name = key($data);
                    $value = $data[$key];
                    $cache = null;
                    if ($isFirst === false) {
                        $cache = "\r\n";
                    } else {
                        $isFirst = false;
                    }
                    $cache .= $value['header'];
                    if (isset($value['content'])) {
                        $cache .= $value['content'];
                    } elseif ($isset($value['file'])) {
                        if ($value['file'] !== '') {
                            $file = fopen($value['file'], 'r');
                            if ($file === false) {
                                throw new Exception;
                            }
                        }
                    }
                    unset($data[$key]);
                } else {
                    $result = fgets($file, $maxLength);
                    if ($result === false) {
                        throw Exception;
                    }
                    if (feof($file)) {
                        fclose($file);
                        $file = null;
                    }
                    if (strlen($result) !== 0) {
                        return $result;
                    }
                }
            }
        };
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
        } else {
            if (self::$oldCurlMultiHandle === null) {
                self::$oldCurlMultiHandle = curl_multi_init();
            }
            curl_multi_add_handle(self::$oldCurlMultiHandle, $this->handle);
            $result = true;
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
                if ($info = curl_multi_info_read(self::$multiHandle)) {
                    if ($info['result'] !== CURLE_OK) {
                        throw new CurlException(
                            curl_error($this->handle), $info['result']
                        );
                    }
                    if ($this->getCurlOption(CURLOPT_RETURNTRANSFER)) {
                        $result = curl_multi_getcontent($this->handle);
                    }
                }
                if ($isRunning && curl_multi_select(self::$isRunning) === -1) {
                    //https://bugs.php.net/bug.php?id=61141
                    usleep(100);
                }
            } while ($running);
            curl_multi_remove_handle(self::$oldCurlMultiHandle, $this->handle);
        }
        $url = $this->getCurlOption(CURLOPT_URL);
        if (is_string($result)
            && $url != null
            && strncmp($url, 'http', 4) === 0
            && $this->getCurlOption(CURLOPT_HEADER) == true
        ) {
            $tmp = explode("\r\n\r\n", $result, 2);
            $this->rawResponseHeaders = $tmp[0];
            $this->responseHeaders = array();
            $isFirst = true;
            foreach (explode("\r\n", $tmp[0]) as $item) {
                if ($isFirst) {
                    $isFirst = false;
                    continue;
                }
                $tmp2 = explode(':', $item, 2);
                $value = null;
                if (isset($tmp2[1])) {
                    $value = $tmp2[1];
                }
                $this->responseHeaders[$tmp2[0]] = $value;
            }
            $result = $tmp[1];
        }
        return $result;
    }

    public function getResponseHeader($name) {
        if (isset($this->responseHeaders[$name])) {
            return $this->responseHeaders[$name];
        }
    }

    public function getResponseHeaders() {
        return $this->responseHeaders;
    }

    public function getRawResponseHeaders() {
        return $this->rawResponseHeaders;
    }

    protected function prepare($options) {
        if (isset($options['headers']) || count($this->headers) !== 0) {
            $headers = null;
            if (isset($options['headers'])) {
                $headers = $options['headers'];
                unset($options['headers']);
            }
            if (isset($this->curlOptions[CURLOPT_HTTPHEADER])) {
                $this->setTemporaryHeaders(
                    $this->curlOptions[CURLOPT_HTTPHEADER], $options
                );
            }
            if (count($this->headers) !== 0) {
                $this->setTemporaryHeaders($this->headers, $options);
            }
            if ($headers !== null) {
                $this->setTemporaryHeaders($headers, $options);
            }
            $headers = array();
            foreach ($options['headers'] as $key => $value) {
                $headers[] = $key . ': ' . $value;
            }
            unset($options['headers']);
            $options[CURLOPT_HTTPHEADER] = $headers;
        }
        if (isset($options['data'])) {
            $this->setData($options['data'], $options);
            unset($options['data']);
        }
        if ($this->isCurlOptionChanged !== true
            && $this->temporaryCurlOptions !== null
            || $this->ignoredCurlOptions
        ) {
            if (self::$isOldCurl === false) {
                curl_reset($this->handle);
            } else {
                curl_close($this->handle);
                $this->handle = curl_init();
            }
        }
        $this->isCurlOptionChanged = false;
        $this->ignoredCurlOptions = null;
        if (isset($options['ignored_curl_options'])) {
            $this->ignoredCurlOptions = $options['ignored_curl_options'];
            unset($options['ignored_curl_options']);
        }
        foreach ($options as $key => $value) {
            if (is_int($key) === false) {
                throw new Exception;
            }
        }
        if ($this->ignoredCurlOptions === null) {
            curl_setopt_array($this->handle, $this->curlOptions);
        } else {
            $tmp = $this->curlOptions;
            foreach ($ignoredCurlOptions as $item) {
                if (is_int($item)) {
                    unset($tmp[$item]);
                }
            }
            curl_setopt_array($this->handle, $tmp);
        }
        if ($options !== null && count($options) !== 0) {
            curl_setopt_array($this->handle, $options);
            $this->temporaryCurlOptions = $options;
        } else {
            $this->temporaryCurlOptions = null;
        }
        $this->rawResponseHeaders = null;
        $this->responseHeaders = null;
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
        $this->ignoredCurlOptions = null;
        $this->isCurlOptionChanged = false;
        $this->rawResponseHeaders = null;
        $this->responseHeaders = null;
        $this->temporaryCurlOptions = null;
        $this->headers = array();
        $this->curlOptions = $this->getDefaultOptions();
        if ($this->curlOptions === null) {
            $this->curlOptions = array();
        }
        if (count($this->curlOptions) !== 0) {
            curl_setopt_array($this->curlOptions);
        }
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

    public function head($url, $headers = null, $options = null) {
        return self::sendHttp('HEAD', $url, null, $headers, $options);
    }

    public function get($url, $headers = null, $options = null) {
        return self::sendHttp('GET', $url, null, $headers, $options);
    }

    public function post($url, $data = null, $headers = null, $options = null) {
        return self::sendHttp('POST', $url, $data, $headers, $options);
    }

    public function patch(
        $url, $data = null,$headers = null,  $options = null
    ) {
        return self::sendHttp('PATCH', $url, $data, $headers, $options);
    }

    public function put($url, $data = null, $headers = null, $options = null) {
        return self::sendHttp('PUT', $url, $data, $headers, $options);
    }

    public function delete($url, $headers = null, $options = null) {
        return self::sendHttp('DELETE', $url, null, $headers, $options);
    }

    public function options($url, $headers = null, $options = null) {
        return self::sendHttp('OPTIONS', $url, null, $headers, $options);
    }
}
