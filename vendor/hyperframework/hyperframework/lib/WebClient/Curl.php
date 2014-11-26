<?php
namespace Hyperframework\WebClient;

use Exception;

class Curl {
    private static $asyncHandle;
    private static $asyncOptions;
    private static $asyncTemporaryOptions;
    private static $asyncRequestOptions;
    private static $asyncPendingRequests;
    private static $asyncMaxHandles;
    private static $asyncProcessingRequests;
    private static $asyncRequestFetchingCallback;
    private static $isOldCurl;
    private $handle;
    private $oldCurlMultiHandle;
    private $options = array();
    private $requestOptions;
    private $rawResponseHeaders;
    private $responseHeaders;
    private $responseCount;

    public static function sendAll(
        array $requests = null,
        $onCompleteCallback = null,
        array $requestOptions = null,
        array $asyncOptions = null
    ) {
        if ($requests !== null && count($requests) !== 0) {
            self::$asyncPendingRequests = $requests;
        } else {
            self::$asyncPendingRequests = null;
        }
        self::$asyncRequestOptions = $requestOptions;
        self::$asyncProcessingRequests = array();
        if (self::$asyncHandle === null) {
            self::$asyncHandle = curl_multi_init();
            if (self::$asyncOptions === null) {
                self::initializeAsyncOptions();
            } else {
                self::setAsyncOptions(self::$asyncOptions);
            }
        } elseif (self::$asyncTemporaryOptions !== null) {
            foreach (self::$asyncTemporaryOptions as $name => $value) {
                if (is_int($name) === false) {
                    continue;
                }
                if (isset(self::$asyncOptions[$name])) {
                    self::setAsyncOption($name, self::$asyncOptions[$name]);
                } else {
                    self::setAsyncOption(
                        $name, self::getDefaultAsyncOptionValue($name)
                    );
                }
            }
        }
        if ($asyncOptions !== null) {
            foreach ($asyncOptions as $name => $value) {
                if (is_int($name)) {
                    if (self::isOldCurl()) {
                        throw new Exception;
                    }
                    curl_multi_setopt(self::$asyncHandle, $name, $value);
                }
            }
        }
        self::$asyncTemporaryOptions = $asyncOptions;
        self::$asyncRequestFetchingCallback = self::getAsyncOption(
            'request_fetching_callback'
        );
        self::$asyncMaxHandles = self::getAsyncOption('max_handles', 1024);
        if (self::$asyncMaxHandles < 1) {
            throw new Exception;
        }
        $status = self::fetchAsyncRequests() !== false;
        if ($status === false) {
            return;
        }
        $sleepTime = self::getAsyncOption('sleep_time');
        if ($sleepTime === null) {
            $sleepTime = 1;
        } else {
            $sleepTime = $sleepTime / 1000;
        }
        $hasPendingRequest = true;
        $isRunning = $status === true;
        for (;;) {
            if ($isRunning) {
                do {
                    $status = curl_multi_exec(self::$asyncHandle, $isRunning);
                } while ($status === CURLM_CALL_MULTI_PERFORM);
                //CURLM_CALL_MULTI_PERFORM is deprecated(curl version 7.20.0)
                if ($status !== CURLM_OK) {
                    $message = '';
                    if (self::isOldCurl() === false) {
                        $message = curl_multi_strerror($status);
                    }
                    throw new CurlException($message, $status, 'multi');
                }
                while ($info = curl_multi_info_read(self::$asyncHandle)) {
                    $handleId = (int)$info['handle'];
                    list($client, $request) =
                        self::$asyncProcessingRequests[$handleId];
                    unset(self::$asyncProcessingRequests[$handleId]);
                    if ($onCompleteCallback !== null) {
                        $context = array('curl_code' => $info['result']);
                        if ($info['result'] !== CURLE_OK) {
                            $context['error'] = curl_error($info['handle']);
                        } else {
                            $context['response'] = $client->initializeResponse(
                                curl_multi_getcontent($info['handle'])
                            );
                            $client->finalize();
                        }
                        $context['request'] = $request;
                        $context['client'] = $client;
                        call_user_func($onCompleteCallback, $context);
                    }
                    curl_multi_remove_handle(
                        self::$asyncHandle, $info['handle']
                    );
                }
                if ($hasPendingRequest) {
                    $status = self::fetchAsyncRequests();
                    if ($status === true) {
                        $isRunning = true;
                        continue;
                    }
                    if ($status === false) {
                        $hasPendingRequest = false;
                    }
                }
                if ($isRunning) {
                    $timeout = null;
                    if ($hasPendingRequest === false
                        || self::$asyncMaxHandles ===
                            count(self::$asyncProcessingRequests)
                    ) {
                        $timeout = PHP_INT_MAX >> 10;
                    } else {
                        $timeout = $sleepTime;
                    }
                    $status = curl_multi_select(self::$asyncHandle, $timeout);
                    //https://bugs.php.net/bug.php?id=61141
                    if ($status === -1) {
                        usleep(100);
                    };
                }
            } else {
                if ($hasPendingRequest === false) {
                    return;
                }
                $time = $sleepTime * 1000;
                for (;;) {
                    $status = self::fetchAsyncRequests();
                    if ($status === false) {
                        return;
                    }
                    if ($status === true) {
                        break;
                    }
                    usleep($time);
                }
            }
        }
    }

    private static function fetchAsyncRequests() {
        $hasNewRequest = false;
        $requestCount = count(self::$asyncProcessingRequests);
        for (; $requestCount <= self::$asyncMaxHandles; ++$requestCount) {
            $request = null;
            if (self::$asyncPendingRequests !== null) {
                $key = key(self::$asyncPendingRequests);
                if ($key !== null) {
                    $request = self::$asyncPendingRequests[$key];
                    unset(self::$asyncPendingRequests[$key]);
                } else {
                    self::$asyncPendingRequests = null;
                }
            }
            if ($request === null) {
                if (self::$asyncRequestFetchingCallback !== null) {
                    $request = call_user_func(
                        self::$asyncRequestFetchingCallback
                    );
                    if ($request === false) {
                        self::$asyncRequestFetchingCallback = null;
                        return $hasNewRequest;
                    }
                    if ($request === null) {
                        if ($hasNewRequest === false) {
                            return null;
                        }
                        return true;
                    }
                } else {
                    return $hasNewRequest;
                }
            }
            $hasNewRequest = true;
            if (is_array($request) === false) {
                $request = array(CURLOPT_URL => $request);
            }
            $class = get_called_class();
            $client = new $class;
            if (self::$asyncRequestOptions !== null) {
                $tmp = $request;
                $request = self::$asyncRequestOptions;
                foreach ($tmp as $name => $value) {
                    $request[$name] = $value;
                }
            }
            $client->prepare($request);
            self::$asyncProcessingRequests[(int)$client->handle] =
                array($client, $request);
            if (curl_multi_add_handle(self::$asyncHandle, $client->handle) !== 0) {
                throw new Exception;//curl exception
            }
        }
    }

    private static function getDefaultAsyncOptionValue($name) {
        if ($name === CURLMOPT_MAXCONNECTS) {
            return 10;
        }
        return null;
    }

    final public static function setAsyncOptions(array $options) {
        if (self::$asyncOptions === null) {
            self::initializeAsyncOptions();
        }
        foreach ($options as $name => $value) {
            self::$asyncOptions[$name] = $value;
            if (self::$asyncTemporaryOptions !== null) {
                unset(self::$asyncTemporaryOptions[$name]);
            }
            if (self::$asyncHandle !== null && is_int($name)) {
                if (self::isOldCurl()) {
                    throw new Exception;
                }
                curl_multi_setopt(self::$asyncHandle, $name, $value);
            }
        }
    }

    private static function initializeAsyncOptions() {
        self::$asyncOptions = array();
        $options = static::getDefaultAsyncOptions();
        if ($options !== null) {
            self::setAsyncOptions($options);
        }
    }

    final public static function setAsyncOption($name, $value) {
        self::setAsyncOptions(array($name => $value));
    }

    final public static function removeAsyncOption($name) {
        self::setAsyncOption(
            $name, self::getDefaultAsyncOptionValue($name)
        );
        unset(self::$asyncOptions[$name]);
    }

    protected static function getDefaultAsyncOptions() {}

    private static function getAsyncOption($name, $default = null) {
        if (self::$asyncTemporaryOptions !== null
            && array_key_exists($name, self::$asyncTemporaryOptions)
        ) {
            return self::$asyncTemporaryOptions[$name];
        } elseif (array_key_exists($name, self::$asyncOptions)) {
            return self::$asyncOptions[$name];
        }
        return $default;
    }

    final public static function closeAsyncHandle() {
        if (self::$asyncHandle === null) {
            return;
        }
        curl_multi_close(self::$asyncHandle);
        self::$asyncHandle = null;
        self::$asyncTemporaryOptions = null;
    }

    final public static function resetAsyncHandle() {
        if (self::$asyncHandle === null) {
            self::$asyncOptions = null;
            self::$asyncTemporaryOptions = null;
            return;
        }
        if (self::$asyncTemporaryOptions !== null) {
            foreach (self::$asyncTemporaryOptions as $name => $value) {
                if (self::$asyncOptions !== null
                    && array_key_exists($name, self::$asyncOptions)
                ) {
                    continue;
                }
                if (is_int($name)) {
                    curl_multi_setopt(
                        self::$asyncHandle,
                        $name,
                        self::getDefaultAsyncOptionValue($name)
                    );
                }
            }
            self::$asyncTemporaryOptions = null;
        }
        if (self::$asyncOptions !== null) {
            foreach (self::$asyncOptions as $name => $value) {
                if (is_int($name)) {
                    curl_multi_setopt(
                        self::$asyncHandle,
                        $name,
                        self::getDefaultAsyncOptionValue($name)
                    );
                }
            }
            self::$asyncOptions = null;
        }
    }

    private static function getFileSize($path) {
        if (PHP_INT_SIZE === 8) {
            $result = filesize($path);
            if ($result === false) {
                throw new Exception;
            }
            return $result;
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
        if (preg_match('/Content-Length: (\d+)/', $header, $matches) === 1) {
            return $matches[1];
        } else {
            throw new Exception;
        }
    }

    final protected function isOldCurl() {
        if (self::$isOldCurl === null) {
            self::$isOldCurl = version_compare(phpversion(), '5.5.0', '<');
        }
        return self::$isOldCurl;
    }

    public function __construct(array $options = null) {
        $defaultOptions = $this->getDefaultOptions();
        if ($defaultOptions === null) {
            $defaultOptions = array();
        } elseif (is_array($defaultOptions) ===  false) {
            throw new Exception;
        }
        if ($options !== null) {
            foreach ($options as $name => $value) {
                $defaultOptions[$name] = $value;
            }
        }
        $this->setOptions($defaultOptions);
    }

    protected function getDefaultOptions() {
        return array(
            CURLOPT_TIMEOUT => 30,
            CURLOPT_CONNECTTIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => 1,
            CURLOPT_AUTOREFERER => 1,
            CURLOPT_MAXREDIRS => 100,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_ENCODING => '',
        );
    }

    public function setOptions(array $options) {
        foreach ($options as $name => $value) {
            if ($name === 'headers') {
                $name = CURLOPT_HTTPHEADER;
            } elseif ($name === 'url') {
                $name = CURLOPT_URL;
            }
            $this->options[$name] = $value;
        }
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

    public function removeOption($name) {
        if ($name === 'headers') {
            $name = CURLOPT_HTTPHEADER;
        } elseif ($name === 'url') {
            $name = CURLOPT_URL;
        }
        unset($this->options[$name]);
    }

    final public function setOption($name, $value) {
        $this->setOptions(array($name => $value));
    }

    final public function getOption($name) {
        if (isset($this->options[$name])) {
            return $this->options[$name];
        }
    }

    final protected function getRequestOption($name) {
        if (isset($this->requestOptions[$name])) {
            return $this->requestOptions[$name];
        }
    }

    private function sendHttp($method, $url, $data, array $options = null) {
        if ($options === null) {
            $options = array();
        }
        if ($data !== null) {
            $options['data'] = $data;
        }
        $options[CURLOPT_URL] = $url;
        $options[CURLOPT_CUSTOMREQUEST] = $method;
        return $this->send($options);
    }

    public function send(array $options = null) {
        if ($options === null) {
            $options = array();
        }
        $this->prepare($options);
        if (self::isOldCurl() === false) {
            $result = curl_exec($this->handle);
            if ($result === false) {
                throw new CurlException(
                    curl_error($this->handle), curl_errno($this->handle)
                );
            }
        } else {
            if ($this->oldCurlMultiHandle === null) {
                $this->oldCurlMultiHandle = curl_multi_init();
            }
            curl_multi_add_handle($this->oldCurlMultiHandle, $this->handle);
            $result = null;
            $isRunning = null;
            do {
                do {
                    $status = curl_multi_exec(
                        $this->oldCurlMultiHandle, $isRunning
                    );
                } while ($status === CURLM_CALL_MULTI_PERFORM);
                //CURLM_CALL_MULTI_PERFORM is deprecated(curl version 7.20.0)
                if ($status !== CURLM_OK) {
                    curl_multi_close($this->oldCurlMultiHandle);
                    $this->oldCurlMultiHandle = null;
                    throw new CurlException('', $status, 'multi');
                }
                if ($info = curl_multi_info_read($this->oldCurlMultiHandle)) {
                    if ($info['result'] !== CURLE_OK) {
                        throw new CurlException(
                            curl_error($this->handle), $info['result']
                        );
                    }
                    $result = curl_multi_getcontent($this->handle);
                }
                if ($isRunning) {
                    $tmp = curl_multi_select(
                        $this->oldCurlMultiHandle, PHP_INT_MAX >> 10
                    );
                    if ($tmp === -1) {
                        //https://bugs.php.net/bug.php?id=61141
                        usleep(100);
                    }
                }
            } while ($isRunning);
            curl_multi_remove_handle($this->oldCurlMultiHandle, $this->handle);
        }
        $result = $this->initializeResponse($result);
        $this->finalize();
        return $result;
    }

    protected function initializeOptions(array &$options) {
        $data = $this->getRequestOption('data');
        if ($data !== null) {
            $this->setData($data, $options);
        }
        $queryParams = $this->getRequestOption('query_params');
        if ($queryParams !== null) {
            if (is_array($queryParams) === false) {
                throw new Exception;
            }
            $url = $this->getRequestOption(CURLOPT_URL);
            if ($url === null) {
                return;
            }
            $queryString = '';
            foreach ($queryParams as $key => $value) {
                if ($queryString !== '') {
                    $queryString .= '&';
                }
                $queryString .= urlencode($key) . '=' . urlencode($value);
            }
            if ($queryString !== '') {
                $queryString = '?' . $queryString;
            }
            $numberSignPosition = strpos($url, '#');
            $questionMarkPosition = strpos($url, '?');
            if ($numberSignPosition === false
                && $questionMarkPosition === false
            ) {
                $url .= $queryString;
            } elseif ($numberSignPosition === false) {
                $url = substr($url, 0, $questionMarkPosition)
                    . $queryString;
            } elseif ($questionMarkPosition === false
                || $numberSignPosition < $questionMarkPosition
            ) {
                $url = substr($url, 0, $numberSignPosition)
                    . $queryString . substr($url, $numberSignPosition);
            } elseif ($numberSignPosition > $questionMarkPosition) {
                $url = substr($url, 0, $questionMarkPosition)
                    . $queryString . substr($url, $numberSignPosition);
            }
            $options[CURLOPT_URL] = $url;
        }
    }

    private function prepare(array $options) {
        $this->rawResponseHeaders = null;
        $this->responseHeaders = null;
        $this->responseCount = null;
        $tmp = $options;
        $options = $this->options;
        foreach ($tmp as $key => $value) {
            if ($key === 'headers') {
                $key = CURLOPT_HTTPHEADER;
            } elseif ($key === 'url') {
                $name = CURLOPT_URL;
            }
            $options[$key] = $value;
        }
        $this->requestOptions =& $options;
        $this->initializeOptions($options);
        $curlOptions = array();
        foreach ($options as $key => $value) {
            if (is_int($key)) {
                $curlOptions[$key] = $value;
            }
        }
        $headers = $this->getHeaders();
        if ($headers !== null && count($headers) !== 0) {
            $tmp = array();
            foreach ($headers as $key => $value) {
                if ($value === null) {
                    continue;
                }
                if (is_array($value)) {
                    foreach ($value as $item) {
                        $tmp[] = $key . ': ' . trim($item, ' ');
                    }
                } else {
                    $tmp[] = $key . ': ' . trim($value, ' ');
                }
            }
            $curlOptions[CURLOPT_HTTPHEADER] = $tmp;
        } else {
            unset($curlOptions[CURLOPT_HTTPHEADER]);
        }
        $this->addCurlCallbackWrapper($curlOptions);
        if ($this->handle !== null && self::isOldCurl() === false) {
            curl_reset($this->handle);
        } else {
            if ($this->handle !== null) {
                curl_close($this->handle);
            }
            $this->handle = curl_init();
        }
        curl_setopt_array($this->handle, $curlOptions);
    }

    private function finalize() {
        $this->requestOptions = null;
    }

    private function getHeaders() {
        if (isset($this->requestOptions[CURLOPT_HTTPHEADER])) {
            $headers = $this->requestOptions[CURLOPT_HTTPHEADER];
            if (is_array($headers) === false) {
                throw new Exception;
            }
            $result = array();
            foreach ($headers as $key => $value) {
                if (is_int($key)) {
                    if (is_array($value) === false) {
                        $value = array($value);
                    }
                    foreach ($value as $key2 => $value2) {
                        if (is_int($key2)) {
                            $tmp2 = explode(':', $value2, 2);
                            $key2 = $tmp2[0];
                            $value2 = '';
                            if (count($tmp2) === 2) {
                                $value2 = $tmp2[1];
                            }
                        }
                        $result[$key2] = $value2;
                    }
                    continue;
                }
                $result[$key] = $value;
            }
            return $result;
        }
    }

    final protected function addRequestHeaders(array $headers) {
        if ($this->requestOptions === null) {
            throw new Exception;
        }
        if (isset($this->requestOptions[CURLOPT_HTTPHEADER]) === false) {
            $this->requestOptions[CURLOPT_HTTPHEADER] = $headers;
            return;
        } elseif (
            is_array($this->requestOptions[CURLOPT_HTTPHEADER]) === false
        ) {
            throw new Exception;
        }
        foreach ($headers as $header) {
            $this->requestOptions[CURLOPT_HTTPHEADER][] = $headers;
        }
    }

    private function setData($data, array &$options) {
        $this->addRequestHeaders(array('Expect', 'Content-Length' => null));
        if (is_array($data) === false) {
            $this->enableCurlPostFieldsOption($options);
            $options[CURLOPT_POSTFIELDS] = $data;
            return;
        }
        if (count($data) === 1) {
            $data = array('type' => key($data), 'content' => reset($data));
        }
        if (isset($data['type']) === false) {
            throw new Exception;
        }
        if ($data['type'] === 'application/x-www-form-urlencoded') {
            $this->enableCurlPostFieldsOption($options);
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
                $this->addRequestHeaders(array('Content-Length' => 0));
                return;
            }
            if (is_array($data['content']) === false) {
                $content = (string)$data['content'];
                $this->enableCurlPostFieldsOption($options);
                $options[CURLOPT_POSTFIELDS] = $content;
                return;
            }
            $data = $data['content'];
            $isSafe = true;
            $shouldUseCurlPostFieldsOption = true;
            $keys = array();
            foreach ($data as $key => $value) {
                if (isset($value['name'])) {
                    $key = $value['name'];
                }
                if (isset($keys[$key])) {
                    $shouldUseCurlPostFieldsOption = false;
                    break;
                }
                $keys[(string)$key] = true;
                if (is_array($value) === false) {
                    $value = (string)$value;
                    if (strlen($value) !== 0 && $value[0] === '@') {
                        if (self::isOldCurl()) {
                            $shouldUseCurlPostFieldsOption = false;
                            break;
                        }
                        $isSafe = false;
                    }
                } else {
                    if (isset($value['content'])) {
                        if (isset($value['type']) || isset($value['file_name']))
                        {
                            $shouldUseCurlPostFieldsOption = false;
                            break;
                        }
                        $value = (string)$value['content'];
                        if (strlen($value) !== 0 && $value[0] === '@') {
                            if (self::isOldCurl()) {
                                $shouldUseCurlPostFieldsOption = false;
                                break;
                            }
                            $isSafe = false;
                        }
                    } elseif (isset($value['file']) && self::isOldCurl()) {
                        if (isset($value['file_name'])
                            && $value['file_name'] !== basename($value['file'])
                        ) {
                            $shouldUseCurlPostFieldsOption = false;
                            break;
                        }
                        if (strpos($value['file'], ';type=') !== false) {
                            $shouldUseCurlPostFieldsOption = false;
                            break;
                        }
                    }
                }
            }
            if ($shouldUseCurlPostFieldsOption) {
                foreach ($data as $key => $value) {
                    if (isset($value['name'])) {
                        $data[$value['name']] = $value;
                        unset($data[$key]);
                    }
                }
                if (self::isOldCurl() === false) {
                    if ($isSafe === false) {
                        $options[CURLOPT_SAFE_UPLOAD] = true;
                    }
                    foreach ($data as $key => &$value) {
                        if (is_array($value)) {
                            if (isset($value['content'])) {
                                $value = (string)$value['content'];
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
                                $value = (string)$value['content'];
                            } elseif (isset($value['file'])) {
                                $value = '@' . $value['file'];
                                if (isset($value['type'])) {
                                    $value .= ';type=' . $value['type'];
                                }
                            }
                        }
                    }
                }
                $this->enableCurlPostFieldsOption($options);
                $options[CURLOPT_POSTFIELDS] = $data;
                return;
            }
            $boundary = 'BOUNDARY-' . sha1(uniqid(mt_rand(), true));
            foreach ($data as $key => &$value) {
                if (isset($value['name'])) {
                    $key = $value['name'];
                }
                $header = '--' . $boundary . "\r\n";
                if (is_array($value) === false) {
                    $value = array('content' => $value);
                }
                if (isset($value['content'])) {
                    $value['content'] = (string)$value['content'];
                }
                $fileName = null;
                if (array_key_exists('file_name', $value)) {
                    $fileName = $value['file_name'];
                } elseif (isset($value['content']) === false
                    && isset($value['file'])) {
                    $fileName = basename($value['file']);
                }
                if ($fileName !== null) {
                    $fileName = '; filename="' . $fileName . '"';
                }
                $type = null;
                if (array_key_exists('type', $value)) {
                    $type = $value['type'];
                } elseif (isset($value['content']) === false
                    && isset($value['file']) === true
                ) {
                    $type = 'application/octet-stream;';
                }
                if ($type !== null) {
                    $type = "\r\nContent-Type: " . $type;
                }
                $header .= 'Content-Disposition: form-data; name="' . $key . '"'
                    . $fileName . $type . "\r\n\r\n";
                $value['header'] = $header;
            }
            $size = $this->getFormDataSize($data, $boundary);
            $this->addRequestHeaders(
                array('Content-Type' =>
                    'multipart/form-data; boundary=' . $boundary)
            );
            $this->addRequestHeaders(array('Content-Length' => $size));
            $this->enableCurlPostFieldsOption($options);
            unset($options[CURLOPT_POSTFIELDS]);
            $options[CURLOPT_READFUNCTION] = $this->getSendFormDataCallback(
                $data, $boundary
            );
        } else {
            $this->addRequestHeaders(array('Content-Type' => $data['type']));
            $data = $data['content'];
            if (isset($data['content'])) {
                $this->enableCurlPostFieldsOption($options);
                $options[CURLOPT_POSTFIELDS] = (string)$data['content'];
            } elseif (isset($data['file'])) {
                $file = fopen($data['file'], 'r');
                if ($file === false) {
                    throw new Exception;
                }
                $size = self::getFileSize($data['file']);
                if ($this->isLargerThanIntMax($size)) {
                    $this->addRequestHeaders(
                        array('Content-Length' => $size)
                    );
                    $this->enableCurlPostFieldsOption($options);
                    unset($options[CURLOPT_POSTFIELDS]);
                    $options[CURLOPT_READFUNCTION] = $this->getSendFileCallback(
                        $file
                    );
                    return;
                }
                unset($options[CURLOPT_READFUNCTION]);
                $options[CURLOPT_UPLOAD] = true;
                $options[CURLOPT_INFILE] = $file;
                $options[CURLOPT_INFILESIZE] = self::getFileSize($data['file']);
            }
        }
    }

    private function enableCurlPostFieldsOption(&$options) {
        unset($options[CURLOPT_UPLOAD]);
        unset($options[CURLOPT_PUT]);
        $options[CURLOPT_POST] = true;
    }

    private function getFormDataSize(array $data, $boundary) {
        $result = 0;
        foreach ($data as $item) {
            $result = $this->addContentLength(strlen($item['header']), $result);
            if (isset($item['content'])) {
                $result = $this->addContentLength(
                    strlen($item['content']), $result
                );
            } elseif (isset($item['file'])) {
                $result = $this->addContentLength(
                    self::getFileSize($item['file']), $result
                );
            }
        }
        $dataCount = count($data);
        $result = $this->addContentLength($dataCount, $result);
        $result = $this->addContentLength($dataCount, $result);
        $result = $this->addContentLength(strlen($boundary) + 6, $result);
        return $result;
    }

    private function isLargerThanIntMax($size) {
        if (is_int($size)) {
            return false;
        }
        $length = strlen($size);
        if ($length < 10) {
            return false;
        }
        if ($length > 10) {
            return true;
        }
        return strcmp($size, PHP_INT_MAX) > 0;
    }

    private function addContentLength($leftOperand, $rightOperand) {
        if (PHP_INT_SIZE === 8) {
            return $leftOperand + $rightOperand;
        }
        $leftOperandString = (string)$leftOperand;
        $rightOperandString = (string)$rightOperand;
        $leftOperandLength = strlen($leftOperandString);
        $rightOperandLength = strlen($rightOperandString);
        if ($leftOperandLength < 10 && $rightOperandLength < 10) {
            return $leftOperand + $rightOperand;
        }
        $result = '';
        $tmp = 0;
        $leftIndex = $leftOperandLength - 1;
        $rightIndex = $rightOperandLength - 1;
        while ($rightIndex >= 0 || $leftIndex >= 0) {
            $left = 0;
            $right = 0;
            if ($leftIndex >= 0) {
                $left = $leftOperandString[$leftIndex];
            }
            if ($rightIndex >= 0) {
                $right = $rightOperandString[$rightIndex];
            }
            $tmp += $left + $right;
            if ($tmp > 9) {
                $result = $tmp - 10 . $result;
                $tmp = 1;
            } else {
                $result = $tmp . $result;
                $tmp = 0;
            }
            --$leftIndex;
            --$rightIndex;
        }
        if ($tmp !== 0) {
            return '1' . $result;
        }
        return $result;
    }

    private function getSendFileCallback($file) {
        return function($handle, $inFile, $maxLength) use (&$file) {
            $result = fgets($file, $maxLength);
            if ($result === false) {
                if (feof($file)) {
                    return;
                }
                throw Exception;
            }
            return $result;
        };
    }

    private function getSendFormDataCallback(array $data, $boundary) {
        $cache = null;
        $file = null;
        $isFirst = true;
        $isEnd = false;
        return function($handle, $inFile, $maxLength) use (
            &$data, &$cache, &$file, &$isFirst, &$isEnd, $boundary
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
                        return "\r\n--" . $boundary . "--\r\n";
                    }
                    $name = key($data);
                    $value = $data[$name];
                    $cache = null;
                    if ($isFirst === false) {
                        $cache = "\r\n";
                    } else {
                        $isFirst = false;
                    }
                    $cache .= $value['header'];
                    if (isset($value['content'])) {
                        $cache .= $value['content'];
                    } elseif (isset($value['file'])) {
                        $file = fopen($value['file'], 'r');
                        if ($file === false) {
                            throw new Exception;
                        }
                    }
                    unset($data[$name]);
                } else {
                    $result = fgets($file, $maxLength);
                    if ($result === false) {
                        if (!feof($file)) {
                            throw new Exception;
                        }
                        $result = '';
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

    protected function initializeResponse($result) {
        if ($result === false) {
            return false;
        }
        $this->responseCount = 1;
        if ($this->getRequestOption(CURLOPT_HEADER) == false
            || is_string($result) === false
        ) {
            return $result;
        }
        $url = $this->getInfo(CURLINFO_EFFECTIVE_URL);
        $tmp = explode('://', $url, 2);
        $protocol = strtolower($tmp[0]);
        if ($protocol === 'http'
            || $protocol === 'https'
            || $protocol === 'file'
            || $protocol === 'ftp'
        ) {
            $headerSize = $this->getInfo(CURLINFO_HEADER_SIZE);
            $this->rawResponseHeaders = substr($result, 0, $headerSize);
            $headers = explode("\r\n", trim($this->rawResponseHeaders));
            $this->responseHeaders = array();
            $current = array();
            foreach ($headers as $header) {
                if ($header === '') {
                    $this->responseHeaders[] = $current;
                    ++$this->responseCount;
                    $current = array();
                }
                if (strpos($header, ':') === false) {
                    continue;
                }
                $tmp = explode(':', $header, 2);
                $value = null;
                if (isset($tmp[1])) {
                    $value = ltrim($tmp[1], ' ');
                }
                if (isset($current[$tmp[0]])) {
                    if (is_array($current[$tmp[0]]) === false) {
                        $current[$tmp[0]] = array($current[$tmp[0]]);
                    }
                    $current[$tmp[0]][] = $value;
                } else {
                    $current[$tmp[0]] = $value;
                }
            }
            $this->responseHeaders[] = $current;
            return substr($result, $headerSize);
        }
        return $result;
    }

    public function getResponseHeader(
        $name, $isMultiple = false, $responseIndex = null
    ) {
        $headers = $this->getResponseHeaders($responseIndex);
        if (isset($headers[$name])) {
            if (is_array($headers[$name])) {
                if ($isMultiple) {
                    return $headers[$name];
                } else {
                    return end($headers[$name]);
                }
            }
            if ($isMultiple) {
                return array($headers[$name]);
            }
            return $headers[$name];
        }
    }

    public function getResponseHeaders($responseIndex = null) {
        if ($this->responseHeaders === null) {
            throw new Exception;
        }
        if ($responseIndex === null) {
            return end($this->responseHeaders);
        } else {
            if (isset($this->responseHeaders[$responseIndex])) {
                return $this->responseHeaders[$responseIndex];
            } else {
                throw new Exception;
            }
        }
    }

    public function getRawResponseHeaders() {
        if ($this->rawResponseHeaders === null) {
            throw new Exception;
        }
        return $this->rawResponseHeaders;
    }

    public function getResponseCount() {
        if ($this->responseCount === null) {
            throw new Exception;
        }
        return $this->responseCount;
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
        if ($this->handle === null || self::isOldCurl()) {
            throw new Exception;
        }
        $result = curl_pause($this->handle, $bitmast);
        if ($result !== CURLE_OK) {
            throw new Exception;
        }
    }

    public function reset() {
        $this->rawResponseHeaders = null;
        $this->responseHeaders = null;
        $this->responseCount = null;
        $this->requestOptions = null;
        $this->options = array();
        $defaultOptions = $this->getDefaultOptions();
        if ($defaultOptions !== null) {
            $this->setOptions($defaultOptions);
        }
    }

    public function close() {
        if ($this->handle === null) {
            throw new Exception;
        }
        curl_close($this->handle);
        $this->handle = null;
        if ($this->oldCurlMultiHandle !== null) {
            curl_multi_close($this->oldCurlMultiHandle);
            $this->oldCurlMultiHandle = null;
        }
    }

    public function __clone() {
        $this->handle = null;
        $this->oldCurlMultiHandle = null;
    }

    public function head($url, array $options = null) {
        return $this->sendHttp('HEAD', $url, null, $options);
    }

    public function get($url, array $options = null) {
        return $this->sendHttp('GET', $url, null, $options);
    }

    public function post($url, $data = null, array $options = null) {
        return $this->sendHttp('POST', $url, $data, $options);
    }

    public function patch($url, $data = null, array $options = null) {
        return $this->sendHttp('PATCH', $url, $data, $options);
    }

    public function put($url, $data = null, array $options = null) {
        return $this->sendHttp('PUT', $url, $data, $options);
    }

    public function delete($url, array $options = null) {
        return $this->sendHttp('DELETE', $url, null, $options);
    }

    public function options($url, array $options = null) {
        return $this->sendHttp('OPTIONS', $url, null, $options);
    }
}
