<?php
namespace Hyperframework\Web\Html;
/*
array(
    'data' => $_GET,
    'name="" action="/product"',
    'method' => 'GET',
    'fields' => array(
        'id' => array(
            'type' => 'Hidden',
        ),
        'amount' => array(
            'type' => 'Number',
            'label' => '总数',
            'min="1" max="100" required',
        )
        'content' => array(
            'max="' . Product::MAX_AGE
                . '" min="' . Product::MIN_AGE . '" required'
            'label' => '内容',
            'pattern' => '',
            'type' => 'TextArea',
        ),
        'category',
        array(
            'type' => 'Submit',
            'value' => '提交'
        ),
    ),
);
//key 对应拦截增强指令，如果没有，则直接通过属性输出,类似 min max 和 required
*/
class FormBuilder {
    public static function render($configs/*, ...*/) {
    }
}

