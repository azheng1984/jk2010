<?php
namespace Hyperframework\Web;

use Hyperframework\Common\Config;
use Hyperframework\Common\ClassNotFoundException;

class ViewFactory {
    public static function createView(array $viewModel = null) {
        $class = Config::get('hyperframework.web.view.class', '');
        if ($class === '') {
            $view = new View($viewModel);
        } else {
            if (class_exists($class) === false) {
                throw new ClassNotFoundException(
                    "View class '$class' does not exist, set using config "
                        . "'hyperframework.web.view.class'."
                );
            }
            $view = new $class($viewModel);
        }
        return $view;
    }
}
