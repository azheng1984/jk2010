<?php
class FilterScreen {
  private $parameters;

  public function render($category) {
    $this->parameters = FilterParameter::getSelectedList();
    echo '<div id="filter">';
    foreach (DbProperty::getList($category['id']) as $item) {
      echo '<div class="property">'.$item['key'].':';
      foreach ($item['values'] as $value) {
        $selected = false;
        if ($this->isSelected($item['id'], $value['id'])) {
          $selected = true;
          echo ' <span class="selected_property">'.$value['value'].' | x</span> ';
        } else {
          if ($_SERVER['QUERY_STRING'] === '') {
            echo ' <a href="?'.urlencode($item['key']).'='.urlencode($value['value']).'">'.$value['value'].'</a>';
          } else {
            echo ' <a href="?'.$_SERVER['QUERY_STRING'].'&'.urlencode($item['key']).'='.urlencode($value['value']).'">'.$value['value'].'</a>';
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
}