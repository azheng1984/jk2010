<?php
namespace Hyperframework\Web\Html;

use Hyperframework\ConfigFileLoader;

class FormBuilder {
    public static function run($data, $config, $errors = null) {
        //todo merge base config
        $formHelper = static::getFormHelper($data, $config, $errors);
        $formHelper->begin();
        foreach ($config[':fields'] as $name => $attrs) {
            if (is_int($name) && isset($attrs[':fields'])) {
                static::renderFieldSet($attrs);
                continue;
            }
            call_user_func(
                array(
                    $formHelper, 'render' . $attrs[':type'],
                    array('name' => $name)
                )
            );
        }
        $formHelper->end();
    }

    protected static function getFormHelper($data, $config, $errors) {
        return new FormHelper($data, $config, $errors);
    }

    protected static function renderFieldSet($config) {
    }
}
