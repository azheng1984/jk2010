<?php
namespace Hyperframework\Cli;

use Hyperframework\ConfigFileLoader;

class App {
    private $config;

    public function __construct() {
        this->config = ConfigFileLoader::loadPhp('app.php');
    }

    public function run() {
    }
}
