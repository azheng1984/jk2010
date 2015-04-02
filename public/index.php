<?php
var_dump(curl_init());
require dirname(__DIR__) . '/vendor/autoload.php';
Hyperframework\Web\App::run();
