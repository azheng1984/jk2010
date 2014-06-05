<?php
return array(
    'data' => $_GET,
    'name="" action="/product"',
    'method' => 'GET',
    'fields' => array(
        'id' => array(
            'type' => 'hidden',
        ),
        'amount' => array(
            'type' => 'number',
            'label' => '总数',
            'min="1" max="100" required',
        ),
        'content' => array(
            'max="' . Product::MAX_AGE
                . '" min="' . Product::MIN_AGE . '" required'
            'label' => '内容',
            'pattern' => '',
            'type' => 'textarea',
        ),
        'category',
        array(
            'id="submit_button"',
            'type' => 'submit',
            'value="提交"',
            'required' => true
        ),
    ),
);
