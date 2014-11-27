<?php
namespace Hyperframework\WebClient;

use Exception;

class Curl {
    const OPT_DATA = 'data';

    protected function getDefaultOptions() {
        return [
//            CURLOPT_TIMEOUT => 30,
//            CURLOPT_CONNECTTIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => 1,
            CURLOPT_AUTOREFERER => 1,
            CURLOPT_MAXREDIRS => 1024,
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_ENCODING => '',
        ];
    }

    public function setOption($name, $value) {
        $this->setOptions(array($name => $value));
    }

    public function setOptions(array $options) {
//        $this->kernel->setOptions($options);
    }

    public function removeOption($name) {
        $this->kernel->removeOptions($options);
    }

    public function resetOptions() {
    }

//    public function getOption($name) {
//        return $this->kernel->getOption($name);
//    }
//
//    public function getOptions() {
//        return $this->kernel->getOptions();
//    }

    private function sendHttp($method, $url, $data, array $options = null) {
        if ($options === null) {
            $options = array();
        }
        if ($data !== null) {
            $options['data'] = $data;
        }
        $options[CURLOPT_URL] = $url;
        $options[CURLOPT_CUSTOMREQUEST] = $method;
    }

    public function getResponseHeader($name, $isMultiple = false) {
    }

    public function getResponseHeaders() {
    }

//    public function getRawResponseHeaders() {
//.    }

//    public function getResponseCount() {
//    }

    public function getResponseInfo($name = null) {
    }

//    public function pause($bitmask) {
//    }

    public function close() {
    }
    
    public function send(array $options = null) {
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
