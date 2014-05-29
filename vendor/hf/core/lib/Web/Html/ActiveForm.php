<?php
class ActiveForm {
    public function isValid() {
        FormBuilder::build(FormConfig::get('product'), $data);
        $filter = new InputFilter(FormConfig::get('product'));
        $product = $filter->getAll();
    }
}
