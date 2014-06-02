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
    'attr' => 'method="POST" action="/product"',
    'fields' => array(
        'id' => array(
            'type' => 'Hidden',
        ),
        'amount' => array(
            'type' => 'number',
            'laber' => '总数',
            'attr' => 'min="1" max="100" required',
        )
        'content' => array(
            'attr' => 'max="' . Product::MAX_AGE
                . '" min="' . Product::MIN_AGE . '" required'
            'label' => '内容',
            'type' => 'TextArea',
        ),
        'category',
        array(
            'type' => 'Submit',
            'value' => '提交'
        ),
    )
));

try {
    FormFilter::execute('product', array(
    ));
    $product = InputFilter::execute(array(
    ), array(
        'validation' => '',
        'form' => 'product'
    ));
} catch (ValidationFailException $ex) {
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
    'validation_config_name' => 'product'
    'data' => $data
));

//hyperframework.web.html.form.html5 = true
//hyperframework.web.html.form.has_id = true

class FormBuilder {
    public static function render($options) {
        if (is_string($options)) {
            $options = array('config_name' => $options);
        }
    }
}
