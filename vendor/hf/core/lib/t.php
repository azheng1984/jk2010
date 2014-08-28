<?php
namespace Hyperframework;

set_time_limit(0);

require './WebClient.php';
require './CurlException.php';
require './CurlMultiException.php';

for ($i = 0; $i < 10; ++$i) {
WebClient::sendAll(array('https://www.google.com.hk'), function($req, $res) {
    print_r($res);
});
}

