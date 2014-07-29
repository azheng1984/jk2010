<?php
namespace Hyperframework\Web\Html;

use Hyperframework\Web\FormConfigLoader;

class FormBuilder {
    public static function run($data, $config, $errors = null) {
        if (is_string($config)) {
            $config = static::getConfig($config);
        }
        $formHelper = static::getFormHelper($data, $config, $errors);
        $formHelper->begin();
        static::renderFields($config);
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

    protected static function getConfig($name) {
        return FormConfigLoader::run($name);
    }

    protected static function getFormHelper($data, $config, $errors) {
        return new FormHelper($data, $config, $errors);
    }

    private static function renderFields($config, $formHelper) {
        foreach ($config as $name => $attrs) {
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
    }

    private static function renderFieldSet($config, $formHelper) {
        echo '<fieldset';
        foreach ($config as $key => $value) {
            if (is_string($key) === false) {
                echo ' ' . $value;
                continue;
            }
            if ($key[0] !== ':') {
                echo ' ' . $key . '="' . $value. '"';
            }
        }
        echo '>';
        static::renderFields($config['fields'], $formHelper);
        echo '</fieldset>';
    }
}
