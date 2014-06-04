<?php
class FormFilter {
    public static function execute($mixed/*, ...*/) {
    }
}
try {
    $data = FormFilter::execute('product', array(
        'permitted_fields' => array(
            'category' => array('pattern' => ''), 'content'
        ),
        'validate_patterns' => true,
//        'patterns' => 'category',
    ));
    Validator::execute($rules, $data);
    //equals to InputFilter::execute($configs);
} catch (ValidationException) {
}
