<?php
namespace Hyperframework\WebClient;

use Exception;

class AsyncCurl {
    const OPT_MAX_HANDLES = 'max_handles';
    const OPT_REQUEST_FETCHING_CALLBACK = 'request_fetching_callback';
    const OPT_ON_COMPLETE = 'on_complete';
    const OPT_REQUEST_OPTIONS = 'request_options';

    protected function getDefaultOptions() {
        return [
            self::OPT_REQUEST_OPTIONS => [
//                CURLOPT_TIMEOUT => 30,
//                CURLOPT_CONNECTTIMEOUT => 30,
                //firefox is 90(about:config network.http.connection-timeout)
                CURLOPT_FOLLOWLOCATION => 1,
                CURLOPT_AUTOREFERER => 1,
                CURLOPT_MAXREDIRS => 1024,
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_ENCODING => '',
            ],
            //'max_handles' => '1000'
        ];
    }

    public function setOption($name, $value) {
    }

    public function setOptions(array $options) {
    }

    public function removeOption($name) {
    }

    public function resetOptions() {
    }

    public function send(array $options = null) {
        $asyncCurl->send([
            'max_handles' => 1024,
            'request_options' => [],
            'sleep_time' => 1000,//ms
            'requests' => [],
            'request_fetching_callback' => function() {
                //return false | null | request
            },
            'on_complete' => function($asyncCurlResponse) {
            },
        ]);
    }

    public function close() {
    }
}
