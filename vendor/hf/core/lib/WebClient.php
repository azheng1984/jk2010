<?php
namespace Hyperframework;

use Exception;

class WebClient {
    private static $multiHandle;
    private static $multiOptions;
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

    public function __construct(array $options = null) {
        if (self::$isOldCurl === null) {
            self::$isOldCurl = version_compare(phpversion(), '5.5.0', '<');
        }
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
                unset(self::$multiPendingRequests[$key]);
            } else {
                self::$multiPendingRequests = null;
            }
        } elseif (self::$multiGetRequestCallback !== null) {
            $request = call_user_func(self::multiGetRequestCallback);
        }
        if ($request === null) {
            return false;
        }
        if (is_array($request) === false) {
            $request = array(CURLOPT_URL => $request);
        }
        if (isset($request['client']) === false) {
            $request['client'] = new WebClient;
        }
        if (self::$multiRequestOptions !== null) {
            foreach (self::$multiRequestOptions as $name => $value) {
                if (array_key_exists($name, $request) === false) {
                    $request[$name] = $value;
                }
            }
        }
        $options = $request;
        $client = $options['client']; 
        unset($options['client']);
        $client->prepare($options);
        self::$multiProcessingRequests[(int)$client->handle] = $request;
        curl_multi_add_handle(self::$multiHandle, $client->handle);
    }

    private static function getDefaultMultiOptionValue($name) {
        if ($name === CURLMOPT_MAXCONNECTS) {
            return 10;
        }
        return null;
    }

    public static function sendAll(
        array $requests = null,
        $onCompleteCallback = null,
        array $requestOptions = null,
        array $multiOptions = null
    ) {
        if ($requests !== null && count($requests) !== 0) {
            self::$multiPendingRequests = $requests;
        } else {
            self::$multiPendingRequests = null;
        }
        self::$multiRequestOptions = $requestOptions;
        self::$multiProcessingRequests = array();
        if (self::$multiHandle === null) {
            self::$multiHandle = curl_multi_init();
            if (self::$multiOptions === null) {
                self::initializeMultiOptions();
            } else {
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
                        $name, self::getDefaultMultiOptionValue($name)
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
        self::$multiGetRequestCallback = self::getMultiOption(
            'get_request_callback'
        );
        $hasPendingRequest = true;
        $maxHandles = self::getMultiOption('max_handles', 100);
        if ($maxHandles < 1) {
            throw new Exception;
        }
        for ($index = 0; $index < $maxHandles; ++$index) {
            $hasPendingRequest = self::addMultiRequest() !== false;
            if ($hasPendingRequest === false) {
                break;
            }
        }
        $selectTimeout = self::getMultiOption('select_timeout', 1);
        if ($selectTimeout <= 0) {
            throw new Exception;
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
                self::closeMultiHandle();
                throw new CurlMultiException($message, $status);
            }
            while ($info = curl_multi_info_read(self::$multiHandle)) {
                $handleId = (int)$info['handle'];
                if ($onCompleteCallback !== null) {
                    $request = self::$multiProcessingRequests[$handleId];
                    $response = array('curl_code' => $info['result']);
                    if ($info['result'] !== CURLE_OK) {
                        $response['error'] = curl_error($info['handle']);
                    }
                    $response['result'] = $request['client']->processResponse(
                        curl_multi_getcontent($info['handle'])
                    );
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

    public static function setMultiOptions(array $options) {
        if (self::$multiOptions === null) {
            self::initializeMultiOptions();
        }
        foreach ($options as $name => $value) {
            self::$multiOptions[$name] = $value;
            if (self::$multiTemporaryOptions !== null) {
                unset(self::$multiTemporaryOptions[$name]);
            }
            if (self::$multiHandle !== null && is_int($name)) {
                if (self::$isOldCurl) {
                    throw new Exception;
                }
                curl_multi_setopt(self::$multiHandle, $name, $value);
            }
        }
    }

    private static function initializeMultiOptions() {
        self::$multiOptions = self::getDefaultMultiOptions();
        if (self::$multiOptions === null) {
            self::$multiOptions = array();
        } elseif (count(self::$multiOptions) !== 0) {
            self::setMultiOptions(self::$multiOptions);
        }
    }

    public static function setMultiOption($name, $value) {
        self::setMultiOptions(array($name => $value));
    }

    public static function removeMultiOption($name) {
        self::setMultiOption(
            $name, self::getDefaultMultiOptionValue($name)
        );
        unset(self::$multiOptions[$name]);
    }

    protected static function getDefaultMultiOptions() {
        return array();
    }

    private static function getMultiOption($name, $default = null) {
        if (self::$multiTemporaryOptions !== null
            && array_key_exists($name, self::$multiTemporaryOptions)
        ) {
            return self::$multiTemporaryOptions[$name];
        } elseif (array_key_exists($name, self::$multiOptions)) {
            return self::$multiOptions[$name];
        }
        return $default;
    }

    public static function closeMultiHandle() {
        if (self::$multiHandle === null) {
            return;
        }
        curl_multi_close(self::$multiHandle);
        self::$multiHandle = null;
        self::$multiOptions = null;
        self::$multiTemporaryOptions = null;
    }

    public static function resetMultiHandle() {
        if (self::$multiHandle === null) {
            self::$multiOptions = null;
            self::$multiTemporaryOptions = null;
            return;
        }
        if (self::$multiTemporaryOptions !== null) {
            foreach (self::$multiTemporaryOptions as $name => $value) {
                if (array_key_exists($name, self::$multiOptions)) {
                    continue;
                }
                if (is_int($name)) {
                    curl_multi_setopt(
                        self::$multiHandle,
                        $name,
                        self::getDefaultMultiOptionValue($name)
                    );
                }
            }
        }
        self::$multiTemporaryOptions = null;
        if (self::$multiOptions !== null) {
            foreach (self::$multiOptions as $name => $value) {
                if (is_int($name)) {
                    curl_multi_setopt(
                        self::$multiHandle,
                        $name,
                        self::getDefaultMultiOptionValue($name)
                    );
                }
            }
        }
        self::$multiOptions = null;
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

    private function addCurlCallbackWrapper(array &$options) {
        foreach ($options as $name => &$value) {
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
        }
    }

    public function setOptions(array $options) {
        if (isset($options['headers'])) {
            $this->setHeaders($options['headers']);
            unset($options['headers']);
        }
        $this->isCurlOptionChanged = true;
        $this->addCurlCallbackWrapper($options);
        foreach ($options as $name => $value) {
            if (is_int($name)) {
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
        if (is_int($name) === false) {
            throw new Exception;
        }
        $this->isCurlOptionChanged = true;
        unset($this->curlOptions[$name]);
    }

    public function setOption($name, $value) {
        $this->setOptions(array($name => $value));
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

    private function sendHttp(
        $method, $url, $data, array $headers = null, array $options = null
    ) {
        if ($options === null) {
            $options = array();
        }
        if ($headers !== null && count($headers) !== 0) {
            if (isset($options['headers'])
                && count($options['headers']) !== 0
            ) {
                foreach ($headers as $key => $value) {
                    if (is_int($key)) {
                        $options['headers'][] = $key;
                    } else {
                        if (array_key_exists($key, $options['headers'])) {
                            unset($options['headers'][$key]);
                        }
                        $options['headers'][$key] = $value;
                    }
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

    private static function getFileSize($path) {
        if (PHP_INT_SIZE === 8) {
            return filesize($path);
        }
        $handle = curl_init('file://' . $path);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($handle, CURLOPT_NOBODY, true);
        curl_setopt($handle, CURLOPT_HEADER, true);
        $header = curl_exec($handle);
        if ($header === false) {
            throw new Exception;
        }
        curl_close($handle);
        if (preg_match('/Content-Length: (\d+)/', $header, $matches)) {
            return $matches[1];
        } else {
            throw new Exception;
        }
    }

    private function setTemporaryHeaders(
        array $headers, array &$options = null
    ) {
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
            if ($key == null) {
                throw new Exception;
            }
            $options['headers'][$key] = $value;
        }
    }

    public function setHeader($name, $value) {
        $this->setHeaders(array($name => $value));
    }

    public function setHeaders(array $headers) {
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
            if ($key == null) {
                throw new Exception;
            }
            $this->headers[$key] = $value;
        }
    }

    public function removeHeader($name) {
        unset($this->headers[$name]);
    }

    private function setData($data, array &$options) {
        $options[CURLOPT_POST] = true;
        $this->setTemporaryHeaders(array('Expect' => null), $options);
        if (is_array($data) === false) {
            $options[CURLOPT_POSTFIELDS] =$data;
            return;
        }
        if (count($data) === 1) {
            $data = array('type' => key($data), 'content' => reset($data));
        }
        if (isset($data['type']) === false) {
            throw new Exception;
        }
        $this->setTemporaryHeaders(
            array('Content-Type' => $data['type']), $options
        );
        if ($data['type'] === 'application/x-www-form-urlencoded') {
            if (is_array($data['content'])) {
                $content = null;
                foreach ($data['content'] as $key => $value) {
                    if ($content !== null) {
                        $content .= '&';
                    }
                    $content .= urlencode($key) . '=' . urlencode($value);
                }
                $options[CURLOPT_POSTFIELDS] = $content;
            } else {
                $options[CURLOPT_POSTFIELDS] = (string)$data['content'];
            }
        } elseif ($data['type'] === 'multipart/form-data') {
            if (isset($data['content']) === false) {
                $this->setTemporaryHeaders(
                    array('Content-Length' => 0), $options
                );
                return;
            }
            if (is_array($data['content']) === false) {
                $content = (string)$data['content'];
                $options[CURLOPT_POSTFIELDS] = $content;
                $this->setTemporaryHeaders(
                    array('Content-Length' => strlen($content)), $options
                );
                return;
            }
            $isSafe = true;
            $shouldUseCurlPostFieldsOption = true;
            foreach ($data['content'] as $key => $value) {
                if (is_array($value) === false) {
                    $value = (string)$value;
                    if (strlen($value) !== 0 && $value[0] === '@') {
                        if (self::$isOldCurl) {
                            $shouldUseCurlPostFieldsOption = false;
                            break;
                        }
                        $isSafe = false;
                    }
                } else {
                    if (isset($value['content'])) {
                        if (isset($value['type'])) {
                            $shouldUseCurlPostFieldsOption = false;
                            break;
                        }
                        $value = (string)$value['content'];
                        if (strlen($value) !== 0 && $value[0] === '@') {
                            if (self::$isOldCurl) {
                                $shouldUseCurlPostFieldsOption = false;
                                break;
                            }
                            $isSafe = false;
                        }
                    } elseif (isset($value['file']) && self::$isOldCurl) {
                        if (isset($value['type'])
                            && $value['type'] !== 'application/octet-stream'
                        ) {
                            $shouldUseCurlPostFieldsOption = false;
                            break;
                        }
                        if (isset($value['file_name'])
                            && $value['file_name'] !== basename($value['file'])
                        ) {
                            $shouldUseCurlPostFieldsOption = false;
                            break;
                        }
                    }
                }
            }
            if ($shouldUseCurlPostFieldsOption) {
                if (self::$isOldCurl === false) {
                    if ($isSafe === false) {
                        $options[CURLOPT_SAFE_UPLOAD] = true;
                    }
                    foreach ($data as $key => &$value) {
                        if (is_array($value)) {
                            if (isset($value['content'])) {
                                $value = $value['content'];
                                continue;
                            } elseif (isset($value['file']) === false) {
                                $value = null;
                                continue;
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
            $this->addIgnoredCurlOption(CURLOPT_POSTFIELDS, $options);
            $boundary = '--BOUNDARY-' . sha1(uniqid(mt_rand(), true));
            $this->setTemporaryHeaders(
                array('Content-Type' => 'multipart/form-data; boundary='
                    . $boundary
                ),
                $options
            );
            $options[CURLOPT_READFUNCTION] = self::getFormDataCallback(
                $data, $boundary
            );
        } else {
            if (isset($data['content'])) {
                $options[CURLOPT_POSTFIELDS] = (string)$data['content'];
                $size = strlen($options[CURLOPT_POSTFIELDS]);
                $this->setTemporaryHeaders(
                    array('Content-Length' => $size), $options
                );
            } elseif (isset($data['file'])) {
                $this->addIgnoredCurlOption(CURLOPT_READFUNCTION, $options);
                $options[CURLOPT_UPLOAD] = true;
                $options[CURLOPT_INFILE] = $data['file'];
                $options[CURLOPT_INFILESIZE] = self::getFileSize($data['file']);
            }
        }
    }

    private function addIgnoredCurlOption($name, array &$options) {
        if (isset($this->curlOptions[$name])) {
            if (isset($options['ignored_curl_optoins']) === false) {
                $options['ignored_curl_options'] = array();
            }
            $options['ignored_curl_options'][] = $name;
        }
    }

    private function getSendFormDataCallback(array $data, $boundary) {
        foreach ($data as $key => &$value) {
            $header = $boundary . "\r\n";
            if (is_array($value) === false) {
                $value = array('content' => $value);
            }
            $fileName = null;
            if (array_key_exists('file_name')) {
                $fileName = $value['file_name'];
            } elseif (isset($value['content']) === false
                && isset($value['file'])) {
                $fileName = basename($value['file']);
            }
            if ($fileName !== null) {
                $fileName = '"; filename="' . $fileName . '"';
            }
            $type = null;
            if (array_key_exists('type', $value)) {
                $type = $value['type'];
            } elseif (isset($value['content']) === false
                && isset($value['file']) === true
            ) {
                $type = 'application/octet-stream';
            }
            if ($type !== null) {
                $type = "\r\nContent-Type: " . $type;
            }
            $header .= 'Content-Disposition: form-data; name="' . $key . '"'
                . $fileName . $type . "\r\n";
            
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
                    } elseif (isset($value['file']) && $value['file'] == null) {
                        $file = fopen($value['file'], 'r');
                        if ($file === false) {
                            throw new Exception;
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
                    if ($result !== '') {
                        return $result;
                    }
                }
            }
        };
    }

    public function send(array $options = null) {
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
            $result = null;
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
                    $result = curl_multi_getcontent($this->handle);
                }
                if ($isRunning && curl_multi_select(self::$isRunning) === -1) {
                    //https://bugs.php.net/bug.php?id=61141
                    usleep(100);
                }
            } while ($running);
            curl_multi_remove_handle(self::$oldCurlMultiHandle, $this->handle);
        }
        return $this->processResponse($result);
    }

    private function processResponse($result) {
        if ($this->getCurlOption(CURLOPT_HEADER) != true) {
            return $result;
        }
        $url = $this->getCurlOption(CURLINFO_HTTP_CODE);
        if (is_string($result)
            && (strncmp($url, 'http:', 5) === 0
                || strncmp($url, 'https:', 6) === 0)
        ) {
            $headerSize = $this->getInfo(CURLINFO_HEADER_SIZE);
            $this->rawResponseHeaders = substr($result, 0, $headerSize);
            $this->responseHeaders = array();
            $headers = explode("\r\n", $this->rawResponseHeaders);
            foreach ($headers as $header) {
                if (strpos($header, ':') === false) {
                    continue;
                }
                $tmp = explode(':', $header, 2);
                $value = null;
                if (isset($tmp[1])) {
                    $value = ltrim($tmp[1], ' ');
                }
                if (isset($this->responseHeaders[$tmp[0]])) {
                    if (is_array($this->responseHeaders[$tmp[0]]) === false) {
                        $this->responseHeaders[$tmp[0]] =
                            array($this->responseHeaders[$tmp[0]]);
                    }
                    $this->responseHeaders[$tmp[0]][] = $value;
                } else {
                    $this->responseHeaders[$tmp[0]] = $value;
                }
            }
            $result = substr($result, $headerSize);
        }
        return $result;
    }

    public function getResponseHeader($name, $isMultiple = false) {
        if (isset($this->responseHeaders[$name])) {
            if (is_array($this->responseHeaders[$name])) {
                if ($isMultiple) {
                    return $this->responseHeaders[$name];
                } else {
                    return end($this->responseHeaders[$name]);
                }
            }
            if ($isMultiple) {
                return array($this->responseHeaders[$name]);
            }
            return $this->responseHeaders[$name];
        }
    }

    public function getResponseHeaders() {
        return $this->responseHeaders;
    }

    public function getRawResponseHeaders() {
        return $this->rawResponseHeaders;
    }

    protected function prepare(array $options) {
        if (isset($options['data'])) {
            $this->setData($options['data'], $options);
            unset($options['data']);
        }
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
                if (is_array($value)) {
                    foreach ($value as $item) {
                        $headers[] = $key . ': ' . $item;
                    }
                } else {
                    $headers[] = $key . ': ' . $value;
                }
            }
            $options[CURLOPT_HTTPHEADER] = $headers;
            unset($options['headers']);
        }
        if ($this->isCurlOptionChanged === true
            || $this->temporaryCurlOptions !== null
            || $this->ignoredCurlOptions !== null
            || $this->handle === null
        ) {
            if ($this->handle !== null && self::$isOldCurl === false) {
                curl_reset($this->handle);
            } else {
                if ($this->handle !== null) {
                    curl_close($this->handle);
                }
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
            foreach ($this->ignoredCurlOptions as $item) {
                if (is_int($item)) {
                    unset($tmp[$item]);
                }
            }
            curl_setopt_array($this->handle, $tmp);
        }
        if ($options !== null && count($options) !== 0) {
            $this->addCurlCallbackWrapper($options);
            curl_setopt_array($this->handle, $options);
            $this->temporaryCurlOptions = $options;
        } else {
            $this->temporaryCurlOptions = null;
        }
        $this->rawResponseHeaders = null;
        $this->responseHeaders = null;
    }

    public function getInfo($name = null) {
        if ($this->handle === null) {
            throw new Exception;
        }
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
        if ($this->handle !== null) {
            $this->handle = curl_copy_handle($this->handle);
        }
    }

    public function head($url, array $headers = null, array $options = null) {
        return self::sendHttp('HEAD', $url, null, $headers, $options);
    }

    public function get($url, array $headers = null, array $options = null) {
        return self::sendHttp('GET', $url, null, $headers, $options);
    }

    public function post(
        $url, $data = null, array $headers = null, array $options = null
    ) {
        return self::sendHttp('POST', $url, $data, $headers, $options);
    }

    public function patch(
        $url, $data = null, array $headers = null, array $options = null
    ) {
        return self::sendHttp('PATCH', $url, $data, $headers, $options);
    }

    public function put(
        $url, $data = null, array $headers = null, array $options = null
    ) {
        return self::sendHttp('PUT', $url, $data, $headers, $options);
    }

    public function delete($url, array $headers = null, array $options = null) {
        return self::sendHttp('DELETE', $url, null, $headers, $options);
    }

    public function options(
        $url, array $headers = null, array $options = null
    ) {
        return self::sendHttp('OPTIONS', $url, null, $headers, $options);
    }
}
