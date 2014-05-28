<?php
namespace Hyperframework\Web;

class Form {
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

$form = new Form($product);
$formMaker->begin('method="POST" action="/article"');
//$formMaker->renderByConfigs($configs);
echo '<label for="category">分类:</label>';
$formMaker->renderInputBox();
$formMaker->end();

$product = null;
ViewContext::push($product);
echo '<form id="category" method="GET" action="/article">';

//显示错误，复杂的是可能的，只在 disable js 的时候才 "统一显示"，否则应该使用 js 验证 + 递交
//ErrorMessage::render();

//render item 必须有 name 或者 "id & name"
$this->renderLabel('category', '分类');
$this->renderTextBox(array(
    'id & name' => 'category',
    'class="doc" onclick="callback()"'
));
FormMaker::renderTextArea(array('name="description" class="bit"'));

echo '</form>';
ViewContext::pop();
