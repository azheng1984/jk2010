<?php
namespace Hyperframework\Web;

class FormMaker {
    public static function renderInput($name) {
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
        $value = ViewContext::get($name);
        if ($value !== null) {
            echo ' value="', $value, '"';
        }
        echo '/>';
    }
}

ViewContext::push($product);
echo '<form id="category" method="GET" action="/article">';

FormMaker::begin('id="category" method="GET" action="/article"', $product);
FormMaker::renderTextInput(array(
    'id', 'name' => 'category', 'class' => 'doc', 'onclick="callback()"'
));
FormMaker::renderTextarea(
    array('id', 'name' => 'description', 'class="bit"')
);
FormMaker::end();

echo '</form>';
ViewContext::pop();
