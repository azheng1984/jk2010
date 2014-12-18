<?php
namespace Hyperframework\Web\Html;

use Hyperframework\Web\CsrfProtection;

class FormHelper {
    private $data;
    private $errors;

    public function __construct(array $data = null, array $errors = null) {
        $this->data = $data;
        $this->errors = $errors;
    }

    public function begin(array $attributes = null) {
        $method = null;
        $formMethod = null;
        if (isset($attributes['method'])) {
            $method = strtoupper($attributes['method']);
            $formMethod = $method;
            if ($formMethod !== null
                && $formMethod !== 'GET'
                && $formMethod !== 'POST'
            ) {
                $attributes['method'] = 'POST';
                $formMethod = 'POST';
            }
        }
        echo '<form';
        if ($attributes !== null) {
            $this->renderAttributes($attributes);
        }
        echo '>';
        if ($method !== $formMethod) {
            $this->renderHiddenField(
                ['name' => '_method', 'value' => $method]
            );
        }
        if ($formMethod === 'POST') {
            $this->renderCsrfProtectionField();
        }
    }

    public function end() {
        echo '</form>';
    }

    public function renderTextField(array $attributes = null) {
        $this->renderInput('text', $attributes);
    }

    public function renderCheckBox(array $attributes = null) {
        $this->renderInput('checkbox', $attributes, 'checked');
    }

    public function renderRadioButton(array $attributes = null) {
        $this->renderInput('radio', $attributes, 'checked');
    }

    public function renderPasswordField(array $attributes = null) {
        $this->renderInput('password', $attributes);
    }

    public function renderHiddenField(array $attributes = null) {
        $this->renderInput('hidden', $attributes);
    }

    public function renderButton(array $attributes = null) {
        $this->renderInput('button', $attributes);
    }

    public function renderSubmitButton(array $attributes = null) {
        $this->renderInput('submit', $attributes);
    }

    public function renderResetButton(array $attributes = null) {
        $this->renderInput('reset', $attributes);
    }

    public function renderFileField(array $attributes = null) {
        $this->renderInput('file', $attributes, null);
    }

    public function renderTextArea(array $attributes = null) {
        echo '<textarea';
        $this->renderAttributes($attributes);
        echo '>';
        if (isset($attributes['name'])
            && isset($this->data[$attributes['name']])
        ) {
            $content = $this->data[$attributes['name']];
            echo self::encodeSpecialChars($content);
        } elseif (isset($attributes[':content'])) {
            echo self::encodeSpecialChars($attributes[':content']);
        }
        echo '</textarea>';
    }

    public function renderSelect(array $attributes = null) {
        echo '<select';
        $this->renderAttributes($attributes);
        echo '>';
        $selectedValue = null;
        if (isset($attributes['name'])
            && isset($this->data[$attributes['name']])
        ) {
            $selectedValue = $this->data[$attributes['name']];
        }
        if (isset($attributes[':options'])) {
            $this->renderOptions($attributes[':options'], $selectedValue);
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
            echo '<span class="error">', self::encodeSpecialChars(
                $this->errors['name']
            ), '</span>';
        }
    }

    public function renderCsrfProtectionField() {
        if (CsrfProtection::isEnabled()) {
            echo '<input type="hidden" name="', CsrfProtection::getTokenName(),
                '" value="', CsrfProtection::getToken(), '"/>';
        }
    }

    protected function encodeSpecialChars($content) {
        //todo config utf-8?
        return htmlspecialchars($content, ENT_QUOTES | ENT_SUBSTITUTE);
    }

    private function renderInput(
        $type, array $attributes = null, $bindingAttribute = 'value'
    ) {
        if ($bindingAttribute === 'value' && isset($attributes['name'])) {
            if (isset($this->data[$attributes['name']])) {
                $attributes['value'] = self::encodeSpecialChars(
                    $this->data[$attributes['name']]
                );
            }
        }
        if ($bindingAttribute === 'checked' && isset($attributes['name'])) {
            if (isset($this->data[$attributes['name']])
                && isset($attributes['value'])
            ) {
                $value = (string)$attributes['value'];
                $data = $this->data[$attributes['name']];
                if ($value == $data && $value === (string)$data) {
                    $attributes['checked'] = 'checked';
                }
            }
        }
        echo '<input type="', $type, '"';
        if ($attributes !== null) {
            $this->renderAttributes($attributes);
        }
        echo '/>';
    }

    private function renderOptions(
        array $options, $selectedValue, $isOptionGroupAllowed = true
    ) {
        foreach ($options as $option) {
            if (is_array($option) === false) {
                $option = array('value' => $option, ':content' => $option);
            } elseif ($isOptionGroupAllowed && isset($option[':options'])) {
                echo '<optgroup';
                $this->renderAttributes($option);
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
            $this->renderAttributes($option);
            $value = (string)$option['value'];
            if ($value == $selectedValue && $value === (string)$selectedValue) {
                echo ' selected="selected"';
            }
            echo '>', self::encodeSpecialChars($option[':content']),
                '</option>';
        }
    }

    private function renderAttributes(array $attributes) {
        foreach ($attributes as $key => $value) {
            if (is_int($key)) {
                echo ' ', $value;
            } elseif ($key[0] !== ':') {
                echo ' ', $key, '="', $value, '"';
            }
        }
    }
}
