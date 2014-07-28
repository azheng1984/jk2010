<?php
namespace Hyperframework\Web\Html;

use Hyperframework\ConfigFileLoader;

class FormBuilder {
    public static function run($data, $config) {
        $formHelper = new FormHelper($data, $config);
        $formHelper->begin();
        foreach ($config['fields'] as $name => $attrs) {
            $type = $attrs['type'];
            unset($attrs['type']);
            array_unshift($attrs, $name);
            call_user_func(array($formHelper, 'render' . $type), $attrs);
        }
        $formHelper->end();
    }
}
