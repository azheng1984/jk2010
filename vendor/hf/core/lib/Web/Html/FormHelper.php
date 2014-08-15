<?php
namespace Hyperframework\Web\Html;

use Hyperframework\ConfigFileLoader;
use Hyperframework\Web\CsrfProtection;

class FormHelper {
    private $data;
    private $errors;
    private $attrs;
    private $fields;

    public function __construct($options = null) {
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
            $config = static::loadConfig($config);
        }
        if (isset($config[':fields'])) {
            $this->fields = $config[':fields'];
            unset($config[':fields']);
        }
        $this->attrs = $config;
    }

    protected static function loadConfig($name) {
        return ConfigFileLoader::getPhp(
            'form' . DIRECTORY_SEPARATOR . $name . '.php'
        );
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
        if (isset($attrs['method'])
            && $attrs['method'] === 'POST'
            && CsrfProtection::isEnabled()
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
        $this->renderInput('checkbox', $attrs, 'checked');
    }

    public function renderRadio($attrs) {
        $this->renderInput('radio', $attrs, 'checked');
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
        $this->renderInput('file', $attrs, null);
    }

    public function renderTextArea($attrs) {
        $attrs = $this->getFullFieldAttrs($attrs);
        echo '<textarea';
        $this->renderAttrs($attrs);
        echo '>';
        if (isset($data[$attrs['name']])) {
            if (isset($attrs[':encode_html_special_chars'])
                && $attrs[':encode_html_special_chars'] === false
            ) {
                echo $data[$attrs['name']];
            } else {
                echo htmlspecialchars($data[$attrs['name']]);
            }
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
        $selectedValue = null;
        if (isset($attrs['name']) && isset($this->data[$attrs['name']])) {
            $selectedValue = $this->data[$attrs['name']];
        }
        if (isset($attrs[':options'])) {
            $this->renderOptions($attrs[':options'], $selectedValue);
        }
        echo '</select>';
    }

    private function renderOptions(
        $options, $selectedValue, $isOptGroupAllowed = true
    ) {
        foreach ($options as $option) {
             if (is_array($option) === false) {
                 $option = array('value' => $option, ':content' => $option);
             } elseif ($isOptGroupAllowed && isset($option[':options'])) {
                 echo '<optgroup';
                 $this->renderAttrs($option);
                 echo '>';
                 $this->renderOptions(
                     $option[':options'], $selectedValue, false
                 );
                 echo '</optgroup>';
                 continue;
             } elseif (isset($option['value']) === false) {
                 continue;
             }
             if (isset($option[':content']) === false) {
                 $option[':content'] = $option['value'];
             }
             echo '<option';
             $this->renderAttrs($option);
             if ($option['value'] === $selectedValue) {
                 echo ' selected="selected"';
             }
             echo '>', $option[':content'], '</option>';
         }
    }

    public function renderError($name = null) {
        if ($name === null) {
            if ($this->errors === null) {
                return;
            }
            foreach (array_keys($this->errors) as $name) {
                $this->renderError($name);
            } 
        } elseif (isset($this->errors[$name])) {
            echo '<span class="error">',
                htmlspecialchars($this->errors['name']), '</span>';
        }
    }

    protected function renderCsrfProtection() {
        echo '<input type="hidden" name="',
            CsrfProtection::getTokenName(),
            '" value="', CsrfProtection::getToken(), '"/>';
    }

    protected function renderInput($type, $attrs, $bindingAttr = 'value') {
        $attrs = self::getFullFieldAttrs($attrs);
        if ($bindingAttr === 'value' && isset($attrs['name'])) {
            if (isset($this->data[$attrs['name']])) {
                if (isset($attrs[':encode_html_special_chars'])
                    && $attrs[':encode_html_special_chars'] === false
                ) {
                    $attrs['value'] = $data[$attrs['name']];
                } else {
                    $attrs['value'] = htmlspecialchars(
                        $this->data[$attrs['name']]
                    );
                }
            }
        }
        if ($bindingAttr === 'checked' && isset($attrs['name'])) {
            if (isset($this->data[$attrs['name']])
                && isset($attrs['value'])
                && $attrs['value'] === $this->data[$attrs['name']]
            ) {
                $attrs['checked'] = 'checked';
            }
        }
        echo '<input type="', $type, '"';
        if ($attrs !== null) {
            $this->renderAttrs($attrs);
        }
        echo '/>';
    }

    private function getFullFieldAttrs($attrs) {
        if ($attrs === null) {
            return;
        }
        $name = null;
        if (is_string($attrs)) {
            $name = $attrs;
            $attrs = array('name' => $name);
        } else {
            if (isset($attrs['name'])) {
                $name = $attrs['name'];
            } elseif ($name === null && isset($attrs['id'])) {
                $name = $attrs['id'];
                $attrs['name'] = $name;
            }
        }
        if ($name === null) {
            return $attrs;
        }
        if (isset($this->fields[$name])) {
            $attrs = array_merge_recursive($this->fields[$name], $attrs);
        }
        return $attrs;
    }

    private function renderAttrs($attrs) {
        foreach ($attrs as $key => $value) {
            if (is_int($key)) {
                echo ' ', $value;
            } elseif ($key[0] !== ':') {
                echo ' ', $key, '="', $value, '"';
            }
        }
    }
}
