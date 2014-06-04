<?php
class FormFilter {
    public static function execute($mixed/*, ...*/) {
    }
}
try {
    $data = FormFilter::execute('product', array(
        'permitted_fields' => array('category', 'content'),
        'use_patterns' => true,
        'patterns' => 'category',
    ));
    Validator::execute($rules, $data);
    //equals to InputFilter::execute($configs, $method = null);
} catch (ValidationException) {
}
