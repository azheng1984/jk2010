<?php
namespace Hyperframework\Web\Html;

class FormBuilder {
    private $formHelper;

    public function __construct($data) {
        $formHelper = new FormHelper($data);
    }

    public function render($config/*, ...*/) {
        foreach (func_get_args() as $config) {
            $formHelper->addConfig($config);
        }
        foreach ($config as $key => $value) {
        }
    }
}
