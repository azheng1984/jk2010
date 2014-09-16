<?php
namespace Hyperframework\Cli;

use Hyperframework\ConfigFileLoader;

class App {
    private $config;

    public function __construct() {
        $options = getopt("f:x", array());
        var_dump($options);
        $this->config = ConfigFileLoader::loadPhp('app.php');
    }

    public function run() {
    }
}
