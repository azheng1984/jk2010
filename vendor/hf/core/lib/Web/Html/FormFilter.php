<?php
try {
    $data = FormFilter::execute('product', array('name', 'category'));
    Validator::execute($rules, $data);
    //equals to InputFilter::execute($configs);
} catch (ValidationException) {
}
