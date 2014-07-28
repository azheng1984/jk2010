<?php
namespace Hyperframework\Web\Html;

class FormHelper {
    private $data;
    private $config;

    public function __construct($data = null, $config = null) {
        $this->data = $data;
        if (isset($config['base'])) {
            $baseConfig = static::getBaseConfig($config['base']);
            //todo: recursive base
            $this->config = array_merge_recursive($baseConfig, $config);
            unset($this->config['base']);
            return;
        }
        $this->config = $config;
    }

    protected static function getBaseConfig($name) {
        return ConfigFileLoader::loadPhp('form/' . $name . '.php');
    }

    public function begin($attrs = null) {
        if (isset($this->config['attrs'])) {
            if ($attrs === null) {
                $attrs = $config['attrs'];
            } else {
                $attrs = array_merge($config['attrs'], $attrs);
            }
        }
        $isCsrfProtectionEnabled = true;
        if (isset($attrs['enable_csrf_protection'])) {
            $isCsrfProtectionEnabled = $attrs['enable_csrf_protection']; 
            unset($attrs['enable_csrf_protection']);
        }
        echo '<form';
        foreach ($attrs as $key => $value) {
            if (is_int($key)) {
                echo ' ', $value;
            } else {
                echo ' ', $key, '="', $name, '"';
            }
        }
        echo '>';
        if (isset($attrs['method'])
            && $attrs['method'] === 'POST'
            && $isCsrfProtectionEnabled !== false
            && Config::get('hyperframework.enable_csrf_protection') !== false
        ) {
            $this->renderCsrfProtectionField();
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
        if (isset($attrs['optgroups'])) {
        }
        if (isset($attrs['options'])) {
            foreach ($attrs['options'] as $option) {
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
