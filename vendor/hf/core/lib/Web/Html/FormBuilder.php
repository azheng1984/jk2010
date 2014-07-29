<?php
namespace Hyperframework\Web\Html;

use Hyperframework\ConfigFileLoader;

class FormBuilder {
    public static function run($data, $config, $errors = null) {
        //todo merge base config
        $formHelper = static::getFormHelper($data, $config, $errors);
        $formHelper->begin();
        foreach ($config[':fields'] as $name => $attrs) {
            $attrs['name'] = $name;
            call_user_func(
                array($formHelper, 'render' . $attrs[':type']), $attrs
            );
        }
        $formHelper->end();
    }

    protected static function getFormHelper($data, $config, $errors) {
        return new FormHelper($data, $config, $errors);
    }
}
