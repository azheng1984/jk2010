<?php
namespace Hyperframework\Web\Html;

use Hyperframework\ConfigFileLoader;

class FormBuilder {
    public static function run($data, $config, $errors = null) {
        //todo merge base config
        $formHelper = static::getFormHelper($data, $config, $errors);
        $formHelper->begin();
        //':fields' => array(
        //    'title' => 'xxx',
        //    array(
        //        ':fields' => array(
        //        ),
        //        'label' => '订单',
        //    )
        //);
        foreach ($config[':fields'] as $name => $attrs) {
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
}
