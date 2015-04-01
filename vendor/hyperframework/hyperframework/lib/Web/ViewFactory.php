<?php
namespace Hyperframework\Web;

use Hyperframework\Common\Config;
use Hyperframework\Common\ClassNotFoundException;

class ViewFactory {
    public static function createView($viewModel = null) {
        $configName = 'hyperframework.web.view.class';
        $class = Config::get($configName, '');
        if ($class === '') {
            $view = new View($viewModel);
        } else {
            if (class_exists($class) === false) {
                throw new ClassNotFoundException(
                    "Class '$class' does not exist, set using config "
                        . "'$configName'."
                );
            }
            $view = new $class($viewModel);
        }
        return $view;
    }
}
