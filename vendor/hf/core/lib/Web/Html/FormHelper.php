<?php
namespace Hyperframework\Web\Html;

use Hyperframework\Web\FormConfigLoader;

class FormHelper {
    private $data;
    private $errors;
    private $attrs;
    private $fields;

    public function __construct($options) {
        if (isset($options['data'])) {
            $this->data = $options['data'];
        }
        if (isset($options['errors'])) {
            $this->data = $options['errors'];
        }
        if (isset($options['config']) === false) {
            return;
        }
        $config = $options['config'];
        if (is_string($config)) {
            $config = static::getConfig($config);
        }
        if (isset($config[':fields'])) {
            $this->fields = $config[':fields'];
            unset($config[':fields']);
        }
        $this->attrs = $config;
    }

    protected static function getConfig($name) {
        return FormConfigLoader::run($name);
    }

    public function begin($attrs = null) {
        if ($this->attrs !== null) {
            if ($attrs === null) {
                $this->attrs = $attrs;
            } else {
                $attrs = array_merge($this->attrs, $attrs);
            }
        }
        echo '<form';
        $this->renderAttrs($attrs);
        echo '>';
        $isCsrfProtectionEnabled = null;
        if (isset($attrs[':enable_csrf_protection'])) {
            $isCsrfProtectionEnabled = $attrs[':enable_csrf_protection'];
        }
        if ($isCsrfProtectionEnabled === null) {
            $isCsrfProtectionEnabled = Config::get(
                'hyperframework.web.enable_csrf_protection'
            );
            Config::redirect('hyperframework.web.enable_csrf_protection');
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
        $this->renderInput('text', $attrs);
    }

    public function renderCheckBox($attrs) {
        $this->renderInput('checkbox', $attrs);
    }

    public function renderRadio($attrs) {
        $this->renderInput('radio', $attrs);
    }

    public function renderPassword($attrs) {
        $this->renderInput('password', $attrs);
    }

    public function renderSubmit($attrs) {
        $this->renderInput('submit', $attrs);
    }

    public function renderReset($attrs) {
        $this->renderInput('reset', $attrs);
    }

    public function renderHidden($attrs) {
        $this->renderInput('hidden', $attrs);
    }

    public function renderButton($attrs) {
        $this->renderInput('button', $attrs);
    }

    public function renderFile($attrs) {
        $this->renderInput('file', $attrs);
    }

    public function renderTextArea($attrs) {
        $attrs = $this->getFullFieldAttrs($attrs);
        echo '<textarea';
        $this->renderAttrs($attrs);
        echo '>';
        if (isset($data[$attrs['name']])) {
            echo htmlspecialchars($data[$attrs['name']]);
        } elseif (isset($attrs[':content'])) {
            echo $attrs[':content'];
        }
        echo '</textarea>';
    }

    public function renderSelect($attrs) {
        $attrs = $this->getFullFieldAttrs($attrs);
        echo '<select';
        $this->renderAttrs($attrs);
        echo '>';
        $value = $data[$attrs['name']];
        foreach ($attrs[':options'] as $option) {
            if (is_array($option) === false) {
                $option = array('value' => $option, ':content' => $option);
            } elseif (isset($option[':content']) === false && isset($option['value'])) {
                $option[':content'] = $option['value'];
            }
            if (isset($option[':options'])) {
                //... render optgroup
            }
            echo '<option';
            $this->renderAttrs($option);
            if ($option['value'] === $value) {
                echo ' selected="selected"';
            }
            echo '>';
            $option[':content'], '</option>'
        }
        echo '</select>';
    }

    public function renderError($name) {
        if (isset($this->errors[$name])) {
            echo '<span class="error">',
                htmlspecialchars($this->errors['name']), '</span>';
        }
    }

    protected function renderCsrfProtection() {
        $this->renderHidden(array('name' => 'csrf', 'value' => ''));
    }

    protected function renderInput($type, $attrs) {
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
            $this->renderAttrs($attrs);
        }
        echo '/>';
    }

    private function getFullFieldAttrs($attrs) {
        if (is_array($attrs) === false) {
            $attrs = array('name' => $attrs);
        }
        if (isset($this->fields[$attrs['name']])) {
            $attrs = array_merge_recursive(
                $this->fields[$attrs['name']], $attrs
            );
        }
        if (isset($attrs['name']) === false && isset($attrs['id'])) {
            $attrs['name'] = $attrs['id'];
        }
        return $attrs;
    }

    private function renderAttrs($attrs) {
        if (is_array($attrs) === false) {
            return;
        }
        foreach ($attrs as $key => $value) {
            if (is_int($key)) {
                echo ' ', $value;
            } elseif ($key[0] !== ':') {
                echo ' ', $key, '="', $name, '"';
            }
        }
    }
}
