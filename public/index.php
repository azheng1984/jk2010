<?php
namespace Hyperframework\Blog;

use Hyperframework\Web\Runner;

require dirname(__DIR__) . '/vendor/autoload.php';
//echo $_SERVER['REQUEST_URI'];
//echo '<a href="/' . urlencode('# $//') . '">x</a>';
Runner::run();
