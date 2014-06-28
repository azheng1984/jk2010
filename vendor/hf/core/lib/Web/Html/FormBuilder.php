<?php
namespace Hyperframework\Web\Html;

class FormBuilder {
    private $formHelper;

    public function __construct($data = null) {
        $this->formHelper = new FormHelper($data);
    }

    public function render($config) {
        $this->formHelper->begin();
        foreach ($config as $attrs) {
            $type = $attrs['type'];
            unset($attrs['type']);
            call_user_func_array(
                array($this->formHelper, 'render'. $type),
                $attrs
            );
        }
        $this->formHelper->end();
    }
}
