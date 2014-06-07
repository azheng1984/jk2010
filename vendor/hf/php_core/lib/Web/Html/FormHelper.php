<?php
namespace Hyperframework\Web\Html;

class FormHelper {
    private $configsList;

    public function __construct($data = null) {
        $this->configsList = array();
        $this->configsList[] = array('data' => $data);
    }

    public function addConfigs($configs) {
    }

    public function static create($configs/*, ...*/) {
        $instance = new FormHelper;
        foreach (func_get_args() as $configs) {
            $instance->addConfigs($configs);
        }
        return $instance;
    }

    public function renderTextBox($name) {
        echo '<input';
        if (isset($attributes['id & name'])) {
            echo ' id="', $attributes['id & name'], '"';
            $name = $attributes['id & name'];
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
