<?php
namespace Hyperframework\WebClient;

use Exception;

class AsyncCurl {
    protected function getDefaultOptions() {
        return [
            'request_options' => [
//                CURLOPT_TIMEOUT => 30,
//                CURLOPT_CONNECTTIMEOUT => 30,
                CURLOPT_FOLLOWLOCATION => 1,
                CURLOPT_AUTOREFERER => 1,
                CURLOPT_MAXREDIRS => 1024,
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_ENCODING => '',
            ],
            //'max_handles' => '1000'
        ];
    }

    public function setOptions() {
    }

    public function setOption($name, $value) {
    }

    public function removeOption($name) {
    }

    public function resetOptions() {
    }

    public function send(array $options = null) {
        $asyncCurl->send([
            'max_handles' => 1000,
            'request_options' => [],
            'sleep_time' => 1000,//ms
            'requests' => [],
            'request_fetching_callback' => function() {
            }
            'on_complete' => function($asyncCurlResponse) {
            },
        ]);
    }

    public function close() {
    }
}
