<?php
namespace Hyperframework\WebClient;

use Exception;

class AsyncCurl {
    const OPT_REQUESTS => 'request';
    const OPT_REQUEST_OPTIONS = 'request_options';
    const OPT_ON_COMPLETE = 'on_complete';
    const OPT_REQUEST_FETCHING_FUNCTION = 'request_fetching_function';
    const OPT_SLEEP_TIME => 'sleep_time',//ms
    const OPT_MAX_HANDLES = 'max_handles';

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
            //self::OPT_MAX_HANDLES => '1024'
        ];
    }

    public function setOption($name, $value) {
    }

    public function setOptions(array $options) {
    }

//    public function removeOption($name) {
//    }
//
//    public function resetOptions() {
//    }

    public function send(array $options = null) {
        $asyncCurl->send([
            self::OPT_MAX_HANDLES => 1024,
            self::OPT_REQUEST_OPTIONS => [
                Curl::OPT_ID => 'xxxxxxx'
            ],
            self::OPT_SLEEP_TIME => 1000,//ms
            self::OPT_REQUESTS => [],
            self::OPT_REQUEST_FETCHING_FUNCTION => function() {
                //return false | null | request
            },
            self::OPT_ON_COMPLETE => function($asyncCurlResponse) {
            },
        ]);
    }

    public function close() {
    }
}
