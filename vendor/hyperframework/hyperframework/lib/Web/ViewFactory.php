<?php
namespace Hyperframework\Web;

use Hyperframework\Common\Config;
use Hyperframework\Common\ClassNotFoundException;

class ViewFactory {
    public static function createView($model) {
        $class = Config::get('hyperframework.web.view.class', '');
        if ($class === '') {
            $view = new View($model);
        } else {
            if (class_exists($class) === false) {
                throw new ClassNotFoundException(
                    "View class '$class' does not exist, set using config "
                        . "'hyperframework.web.view.class'."
                );
            }
            $view = new $class($model);
        }
        return $view;
    }
}
