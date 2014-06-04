<?php
class FormFilter {
    public static function execute($configs/*, ...*/) {
    }
}

try {
    $data = FormFilter::execute('product', array(
        'permitted_fields' => array(
            'category' => array('pattern' => ''), 'content'
        ),
//        'validate_patterns' => true,
//        'patterns' => 'category',
    ));
    //equals to InputFilter::execute($configs);
} catch (ValidationException) {
}
