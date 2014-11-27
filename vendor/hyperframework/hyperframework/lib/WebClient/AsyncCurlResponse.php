<?php
namespace Hyperframework\WebClient;

class AsyncCurlResponse {
    private $handle;
    private $code;
    private $headers;
    private $content;

    public function __construct($handle, $code) {
    }

    public function getCode() {
        return $this->code;
    }

    public function hasError() {
        return $this->code !== CURLE_OK;
    }

    public function getErrorMessage() {
        if ($this->hasError()) {
            return curl_error($this->getHandle());
        }
    }

    public function getContent() {
        if ($this->content === null) {
            $this->content = curl_multi_getcontent($this->getHandle()); //return null?
        }
        return $this->content;
    }

    public function getInfo($name = null) {
        return curl_getinfo($this->getHandle(), $name);
    }

//    public function getRequestOptions() {
//        return $this->requestOptions;
//    }
//
//    public function hasRequestOption($name) {
//    }
//
//    public function getRequestOption($name) {
//    }

    public function getHeader($name, $isMultiple = false) {
    }

    public function getHeaders() {
    }

//    public function getRawHeaders() {
//    }

    public function close() {
        $handle = $this->getHandle();
        curl_close($handle);
        $this->handle = null;
    }

    private function getHandle() {
        if ($this->handle === null) {
            throw new Exception;
        }
        return $this->handle;
    }
}
