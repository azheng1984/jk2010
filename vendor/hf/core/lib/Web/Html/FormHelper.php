<?php
namespace Hyperframework\Web\Html;

class FormHelper {
    private $data;
    private $attrs;
    private $fields;
    private $validationRules;

    public function __construct($data = null, $config = null) {
        $this->data = $data;
        if ($config === null) {
            return;
        }
        if (isset($config[':base'])) {
            $baseConfig = static::getBaseConfig($config[':base']);
            //todo: recursive base
            $config = array_merge_recursive($baseConfig, $config);
            unset($config[':base']);
        }
        if (isset($config[':fields'])) {
            $this->fields = $config[':fields'];
            unset($config[':fields']);
        }
        if (isset($config[':validation_rules'])) {
            $this->validtionRules = $config[':validation_rules'];
            unset($config[':validation_rules']);
        }
        $this->attrs = $config;
    }

    protected static function getBaseConfig($name) {
        return ConfigFileLoader::loadPhp('form/' . $name . '.php');
    }

    public function begin($attrs = null) {
        if (is_array($this->attrs)) {
            $attrs = array_merge($this->attrs, $attrs);
        }
        echo '<form';
        foreach ($attrs as $key => $value) {
            if (is_int($key)) {
                echo ' ', $value;
            } elseif ($key[0] !== ':') {
                echo ' ', $key, '="', $name, '"';
            }
        }
        echo '>';
        $isCsrfProtectionEnabled = null;
        if (isset($attrs[':enable_csrf_protection'])) {
            $isCsrfProtectionEnabled = $attrs[':enable_csrf_protection'];
        }
        if ($isCsrfProtectionEnabled === null) {
            $isCsrfProtectionEnabled = Config::get(
                'hyperframework.enable_csrf_protection'
            );
        }
        if (isset($attrs['method'])
            && $attrs['method'] === 'POST'
            && $isCsrfProtectionEnabled !== false
        ) {
            $this->renderCsrfProtection();
        }
    }

    public function end() {
        echo '</form>';
    }

    public function renderText($attrs) {
        $attrs['type'] = 'text';
        $this->renderInput($attrs);
    }

    public function renderCheckBox($attrs) {
        $attrs['type'] = 'checkbox';
        $this->renderInput($attrs);
    }

    public function renderRadio($attrs) {
        $attrs['type'] = 'radio';
        $this->renderInput($attrs);
    }

    public function renderPassword($attrs) {
        $attrs['type'] = 'password';
        $this->renderInput($attrs);
    }

    public function renderSubmit($attrs) {
        $attrs['type'] = 'submit';
        $this->renderInput($attrs);
    }

    public function renderReset($attrs) {
        $attrs['type'] = 'reset';
        $this->renderInput($attrs);
    }

    public function renderHidden($attrs) {
        $attrs['type'] = 'hidden';
        $this->renderInput($attrs);
    }

    public function renderButton($attrs) {
        $attrs['type'] = 'button';
        $this->renderInput($attrs);
    }

    public function renderFile($attrs) {
        $attrs['type'] = 'file';
        $this->renderInput($attrs);
    }

    public function renderTextArea($attrs) {
        echo '<textarea';
        echo '>';
        if (isset($data[$attrs['name']])) {
            echo $data[$attrs['name']];
        } else if (isset($attrs['value'])) {
            echo $attrs['value'];
        }
        echo '</textarea>';
    }

    public function renderSelect($attrs) {
        $attrs = array_merge_recursive($this->config[$attrs['name']], $attrs);
        echo '<select';
        echo '>';
        $value = $data[$attrs['name']];
        if (isset($attrs[':optgroups'])) {
        } elseif (isset($attrs[':options'])) {
            foreach ($attrs[':options'] as $option) {
                echo '<option'
                if ($option['value'] === $value) {
                }
                echo '<option>', $option['name'], '</option>'
            }
        }
        echo '</select>';
    }

    protected function renderCsrfProtection() {
        $this->renderHidden(array('name' => 'csrf', 'value' => ''));
    }

    protected function renderInput($attrs) {
        $name = null;
        if (isset($attrs['name'])) {
            $name = $attrs['name'];
        } elseif (isset($attrs['id'])) {
            $name = $attrs['id'];
        }
        if ($name !== null) {
            if (isset($this->config['fields'][$name])) {
                $attrs = array_merge($this->config['fields'][$name], $attrs);
            }
        }
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
}
