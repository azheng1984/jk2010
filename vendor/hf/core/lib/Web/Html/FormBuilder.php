<?php
namespace Hyperframework\Web\Html;

//form rendering definition
array(
    'category' => array(
        //rendering
        'has_id' => true,
//        'type' => 'email',
        'label' => '分类',
        //validation
        //'format' => 'email',
        'default' => '不限'
    ),
    'submit' => array(
        'value' => '提交',
    ),
)

//validation
array(
    'category' => array(
        'max_length' => '100',
//      'min_length' => '20',
        'required' => true,
    )
);

//other input filter

FormBuilder::render(array(
    'data' => $_GET,
    'method' => 'GET',
    'action="/product"',
    'fields' => array(
        'id' => array(
            'type' => 'Hidden',
        ),
        'amount' => array(
            'type' => 'number',
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
    )
));

$form = new Form($data, 'product');
$form = new FormBuilder(array(
    'data' => $data,
    'method' => 'GET',
    'input_filter_configs' => 'product',
));

try {
    FormFilter::execute('product', array(
        'check_patterns' => true,
        'ignored_patterns' => 'category',
        'inclusions' => array('category', 'content'),
    ));
    Validator::execute('', $data);
    $product = InputFilter::execute(
    );
} catch (ValidationException $ex) {
}

InputFilterCollection::execute(
    new FormFilter('product'),
    new InputFilter(array(
        '' => '',
        '' => ''
    ))
);

class ProductFormBuilder {
    public function getRenderingConfig() {
    }

    public function getValidationConfig() {
    }
}

$product = InputFilter::execute(
    $config, ProductForm::getValidationConfig()
);

InputFilter::execute($config);

//type="email"
$product = InputFilter::execute(array(
    'data' => $_GET,
    'validation_config_name' => 'product',
    'extra_fields' => array()
));

//min max require

try {
    $product = $filter->get('title', 'category');
}

$form = new Form(array(
    'validation_configs' => 'product'
    'data' => $data
));

new Form($data);

//hyperframework.web.html.form.html5 = true
//hyperframework.web.html.form.has_id = true

class FormBuilder {
    public static function render($options) {
        if (is_string($options)) {
            $options = array('config_name' => $options);
        }
    }
}
