<?php
namespace Hyperframework\Web;

class FormMaker {
    private $data;

    public function __construct($data = null) {
        $this->data = $data;
    }

    public function renderTextBox($name) {
        echo '<input';
        if (isset($attributes['id'])) {
            if ($attributes['id'] === true) {
                $attributes['id'] = $name;
            }
            echo ' id="', $attributes['id'], '"';
            unset($attributes['id']);
        }
        echo ' name="', $name, '"';
        if ($attributes !== null) {
            foreach ($attributes as $key => $value) {
                echo ' ="', $name, '"';
            }
        }
        if (empty($this->data[$name]) === false) {
            echo ' value="', $value, '"';
        }
        echo '/>';
    }

    public function renderCsrfProtectionField() {
    }

    public function begin($attributes = null) {
    }

    public function end() {
        echo '</form>';
    }

    protected static function get($name) {
        if (isset($this->data[$name])) {
            return $this->data[$name];
        }
    }
}

$formMaker = new FormMaker($product);
$formMaker->renderInputBox();

$formMaker = new FormMaker($product);
$formMaker->begin('method="POST" action="/article"');
$formMaker->renderInputBox();
//echo '</form>';
$formMaker->end();

$product = null;
ViewContext::push($product);
echo '<form id="category" method="GET" action="/article">';

//显示错误，复杂的是可能的，只在 disable js 的时候才 "统一显示"，否则应该使用 js 验证 + 递交
//ErrorMessage::render();

FormMaker::renderTextBox(array(
    'id', 'name' => 'category', 'class' => 'doc', 'onclick="callback()"'
));
FormMaker::renderTextArea(array('id', 'name' => 'description', 'class="bit"'));

echo '</form>';
ViewContext::pop();
