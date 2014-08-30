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
    private $options = array();
    private $temporaryOptions;
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
                $handleId = intval($info['handle']);
                if ($onCompleteCallback !== null) {
                    $request = self::$multiProcessingRequests[$handleId];
                    $response = array('curl_code' => $info['result']);
                    if ($info['result'] !== CURLE_OK) {
                        $response['error'] =
                            curl_error($info['handle']);
                    }
                    if ($request['client']->getOption(CURLOPT_RETURNTRANSFER)) {
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
            CURLOPT_TIMEOUT => 3,
            CURLOPT_CONNECTTIMEOUT => 3,
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

    private function sendHttp($method, $url, $data, $headers, $options) {
        if ($options === null) {
            $options = array();
        }
        if ($headers !== null && count($headers) !== 0) {
            curl_setopt(CURLOPT_POST, true);
            $defaultHeaders = self::getOption(CURLOPT_HTTPHEADER);
            if ($defaultHeaders !== null) {
                //todo merge same key headers
                foreach ($headres as $header) {
                    $defaultHeaders[] = $header;
                }
                $header = $defaultHeaders;
            }
        }
        if ($data !== null) {
            self::setData($data);
        }
        $options[CURLOPT_URL] = $url;
        $options[CURLOPT_CUSTOMREQUEST] = $method;
        return self::send($options);
    }

    private function mergeHeaders($headers, &$options) {
    }

    $client->post('xx', array(
        'type' => 'multipart/form-data',
        'content' => array(
            'file' => array('file' => '/xx')
        ),
        'file' => 'xx'
    ));

    private function setData($data, &$options) {
        if (is_string($data)) {
            $options[CURLOPT_POSTFIELDS] = $data;
            return;
        }
        if (count($data) === 1) {
            $data = array('type' => key($data), 'content' => reset($data));
        }
        if ($data['type'] === 'application/x-www-form-urlencoded') {
            if (is_array($data)) {
                //todo array to string & urlencode
            } else {
                $options[CURLOPT_POSTFIELDS] = $data['content'];
            }
        } elseif ($data['type'] !== 'multipart/form-data') {
            if (isset($data['content'])) {
                $options[CURLOPT_POSTFIELDS] = $data[''];
            }
            if (isset($data['content'])) {
                $options[CURLOPT_POSTFIELDS] = $data;
                //todo add content type header
                return;
            } elseif (isset($data['file'])) {
                $options[CURLOPT_READFUNCTION] = self::getSendContentCallback($data);
            }
        } else {
            if (self::$isOldCurl) {
                foreach ($data as $key => $value) {
                    if (is_array($key) || $value[0] === '@') {
                        //todo check postfields has been set? if has been set reset handle()
                        //$this->temporaryOptions['ignored_options'] = array(CURLOPT_POSTFIEDLS);
                        $boundary = '--BOUNDARY-' . sha1(uniqid(mt_rand(), true));
                        //set content type header
                        $options[CURLOPT_READFUNCTION] = self::getFormDataCallback($data, $boundary);
                        return;
                    }
                }
            } else {
                $options[CURLOPT_SAFE_UPLOAD] = true;
                foreach ($data as $key => &$value) {
                    if (is_array($value)) {
                        if (isset($value['file']) === false) {
                            throw new Exception;
                        }
                        $type = null;
                        if (isset($value['type'])) {
                            $type = $value['type'];
                        }
                        $value = curl_file_create($value['file'], $type, $key);
                    }
                }
            }
            $options[CURLOPT_POSTFIELDS] = $data;
        }
    }

    private function getSendContentCallback($data) {
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
                    fclose($handle);
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
            return $result;
        }
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
                if ($this->getOption(CURLOPT_RETURNTRANSFER)) {
                    $result = curl_multi_getcontent($this->handle);
                }
            }
            if ($isRunning && curl_multi_select(self::$isRunning) === -1) {
                //https://bugs.php.net/bug.php?id=61141
                usleep(100);
            }
        } while ($running);
        curl_multi_remove_handle(self::$oldCurlMultiHandle, $this->handle);
        return $result;
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
        $this->options = $this->getDefaultOptions();
        if ($this->options === null) {
            $this->options = array();
        }
        if (count($this->options) !== 0) {
            curl_setopt_array($this->options);
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
