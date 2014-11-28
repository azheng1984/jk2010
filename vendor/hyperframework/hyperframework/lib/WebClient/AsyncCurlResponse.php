<?php
namespace Hyperframework\WebClient;

use Exception;

class AsyncCurlResponse {
    private $handle;
    private $code;
    private $id;
    private $headers;
    private $content;

    public function __construct($handle, $code, $id) {
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

    public function getId() {
        return $this->id;
    }

    public function getContent() {
        if ($this->hasError()) {
            throw new Exception;
        }
        if ($this->content === null) {
            $this->content = curl_multi_getcontent($this->getHandle());
            if ($this->content === null) {
                throw new Exception; //may be never happen
            }
        }
        return $this->content;
    }

    public function getInfo($name = null) {
        if ($this->hasError()) {
            throw new Exception;
        }
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
        if ($this->hasError()) {
            throw new Exception;
        }
    }

    public function getHeaders() {
        if ($this->hasError()) {
            throw new Exception;
        }
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
