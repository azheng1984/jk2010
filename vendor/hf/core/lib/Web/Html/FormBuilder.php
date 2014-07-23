<?php
namespace Hyperframework\Web\Html;

class FormBuilder {
    public static function run($data, $config) {
        FormBuilder::run($article, 'article');
        //merge config
        $formHelper = new FormHelper($data, $config);
        $formHelper->begin();
        foreach ($config as $attrs) {
            $type = $attrs['type'];
            unset($attrs['type']);
            call_user_func_array(
                array($formHelper, 'render' . $type),
                $attrs
            );
        }
        $formHelper->end();
    }
}
