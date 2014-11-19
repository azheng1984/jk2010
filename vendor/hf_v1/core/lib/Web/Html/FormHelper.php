<?php
namespace Hyperframework\Web\Html;

use Hyperframework\ConfigFileLoader;
use Hyperframework\Web\CsrfProtection;

class FormHelper {
    private $data;
    private $errors;

    public function __construct(array $data = null, array $errors = null) {
        $this->data = $data;
        $this->errors = $errors;
    }

    public function begin(array $attrs = null) {
        echo '<form';
        if ($attrs !== null) {
            $this->renderAttrs($attrs);
        }
        echo '>';
        if (isset($attrs['method'])
            && $attrs['method'] === 'POST'
            && CsrfProtection::isEnabled()
        ) {
            $this->renderCsrfProtectionField();
        }
    }

    public function end() {
        echo '</form>';
    }

    public function renderTextField(array $attrs) {
        $this->renderInput('text', $attrs);
    }

    public function renderCheckBox(array $attrs) {
        $this->renderInput('checkbox', $attrs, 'checked');
    }

    public function renderRadioButton(array $attrs) {
        $this->renderInput('radio', $attrs, 'checked');
    }

    public function renderPasswordField(array $attrs) {
        $this->renderInput('password', $attrs);
    }

    public function renderHiddenField(array $attrs) {
        $this->renderInput('hidden', $attrs);
    }

    public function renderButton(array $attrs) {
        $this->renderInput('button', $attrs);
    }

    public function renderSubmitButton(array $attrs) {
        $this->renderInput('submit', $attrs);
    }

    public function renderResetButton(array $attrs) {
        $this->renderInput('reset', $attrs);
    }

    public function renderFileField(array $attrs) {
        $this->renderInput('file', $attrs, null);
    }

    public function renderTextArea(array $attrs) {
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
                echo self::encodeHtmlSpecialChars($data[$attrs['name']]);
            }
        } elseif (isset($attrs[':content'])) {
            echo $attrs[':content'];
        }
        echo '</textarea>';
    }

    public function renderSelect(array $attrs) {
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

    public function renderError($name = null) {
        if ($name === null) {
            if ($this->errors === null) {
                return;
            }
            foreach (array_keys($this->errors) as $name) {
                $this->renderError($name);
            } 
        } elseif (isset($this->errors[$name])) {
            echo '<span class="error">', self::encodeHtmlSpecialChars(
                $this->errors['name']
            ), '</span>';
        }
    }

    public function renderCsrfProtectionField() {
        echo '<input type="hidden" name="',
            CsrfProtection::getTokenName(),
            '" value="', CsrfProtection::getToken(), '"/>';
    }

    private function encodeHtmlSpecialChars($content) {
        return htmlspecialchars($content, ENT_QUOTES | ENT_SUBSTITUTE);
    }

    private function renderInput($type, array $attrs, $bindingAttr = 'value') {
        $attrs = self::getFullFieldAttrs($attrs);
        if ($bindingAttr === 'value' && isset($attrs['name'])) {
            if (isset($this->data[$attrs['name']])) {
                if (isset($attrs[':encode_html_special_chars'])
                    && $attrs[':encode_html_special_chars'] === false
                ) {
                    $attrs['value'] = $data[$attrs['name']];
                } else {
                    $attrs['value'] = self::encodeHtmlSpecialChars(
                        $this->data[$attrs['name']]
                    );
                }
            }
        }
        if ($bindingAttr === 'checked' && isset($attrs['name'])) {
            if (isset($this->data[$attrs['name']]) && isset($attrs['value'])) {
                $value = strval($attrs['value']);
                if ($value === strval($this->data[$attrs['name']])) {
                    $attrs['checked'] = 'checked';
                }
            }
        }
        echo '<input type="', $type, '"';
        if ($attrs !== null) {
            $this->renderAttrs($attrs);
        }
        echo '/>';
    }

    private function renderOptions(
        array $options, $selectedValue, $isOptGroupAllowed = true
    ) {
        if ($selectedValue !== null) {
            $selectedValue = strval($selectedValue);
        }
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
            if ($selectedValue !== null
                && strval($option['value']) === $selectedValue
            ) {
                echo ' selected="selected"';
            }
            echo '>', $option[':content'], '</option>';
        }
    }

    private function getFullFieldAttrs(array $attrs) {
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
        return $attrs;
    }

    private function renderAttrs(array $attrs) {
        foreach ($attrs as $key => $value) {
            if (is_int($key)) {
                echo ' ', $value;
            } elseif ($key[0] !== ':') {
                echo ' ', $key, '="', $value, '"';
            }
        }
    }
}
