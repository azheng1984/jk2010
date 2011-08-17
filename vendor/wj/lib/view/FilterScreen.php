<?php
class FilterScreen {
  private $parameters;

  public function render($category) {
    $this->parameters = FilterParameter::getSelectedList();
    echo '<div id="filter">';
    foreach (DbProperty::getList($category['id']) as $item) {
      echo '<div class="property">'.$item['key'].':';
      $propertySelected = false;
      foreach ($item['values'] as $value) {
        $selected = false;
        if ($this->isSelected($item['id'], $value['id'])) {
          $selected = true;
          $propertySelected = true;
          echo ' <span class="selected_property">'.$value['value'].' | x</span> ';
        } else {
          if ($_SERVER['QUERY_STRING'] === '') {
            echo ' <a href="?'.urlencode($item['key']).'='.urlencode($value['value']).'">'.$value['value'].'</a>';
          } else {
            echo ' <a href="'.$this->appendFilterUrl($item['key'], $value['value']).'">'.$value['value'].'</a>';
          }
        }
      }
      echo '</div>';
    }
    echo '</div>';
  }

  private function isSelected($keyId, $valueId) {
    foreach ($this->parameters as $parameter) {
      if ($keyId === $parameter[0]['id'] && $valueId === $parameter[1]['id']) {
        return true;
      }
    }
  }

  private function appendFilterUrl($key, $value) {
    $list = array();
    $isInserted = false;
    foreach ($this->parameters as $parameter) {
      if ($key === $parameter[0]['key'] && $value !== $parameter[1]['value']) {
        $list[] = urlencode($parameter[0]['key']).'='.urlencode($parameter[1]['value']).':'.urlencode($value);
        $isInserted = true;
        continue;
      }
      $list[] = urlencode($parameter[0]['key']).'='.urlencode($parameter[1]['value']);
    }
    if (!$isInserted) {
      $list[] = urlencode($key).'='.urlencode($value);
    }
    if (count($list) !== 0) {
      return '?'.implode('&', $list);
    }
  }
}