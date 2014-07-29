<?php
namespace Hyperframework\Web\Html;

use Hyperframework\ConfigFileLoader;

class FormConfigLoader {
    public static function run($name) {
        return ConfigFileLoader::getPhp('form' . $name . '.php');
    }
}
