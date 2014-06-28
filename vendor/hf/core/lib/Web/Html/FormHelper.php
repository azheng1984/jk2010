<?php
namespace Hyperframework\Web\Html;

class FormHelper {
    private $data;
    private $configs = array();

    public function __construct($data = null) {
        $this->data = $data;
    }

    public function addConfig($config) {
    }

    public static function create($options) {
    }

    public function renderTextBox($attrs) {
        echo '<input';
        $name = null;
        if (is_string($attrs)) {
            $name = $attrs;
            $attrs = null;
        } else {
            if (isset($attrs['id'])) {
                echo ' id="', $attrs['id'], '"';
                if (isset($attrs['name']) === false) {
                    $name = $attrs['id'];
                } else {
                    $name = $attrs['name'];
                }
                unset($attrs['id']);
            } else {
                $name = $attrs['name'];
            }
        }
        echo ' name="', $name, '"';
        if (empty($this->data[$name]) === false) {
            echo ' value="', $this->data[$name], '"';
        }
        if ($attrs !== null) {
            foreach ($attrs as $key => $value) {
                if (is_int($key)) {
                    echo ' ', $value;
                }
                echo ' ', $key, '="', $name, '"';
            }
        }
        echo '/>';
    }

    public function renderCsrfProtectionField() {
        echo '<input type="hidden" name="csrf" value="xxx"/>';
    }

    public function begin($attrs = null) {
        echo '<form>';
        $this->renderCsrfProtectionField();
    }

    public function end() {
        echo '</form>';
    }
}
