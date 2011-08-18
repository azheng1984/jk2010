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
          echo ' <span class="selected_property">'.$value['value'].' |<a class="cancel" href="'.$this->removeFilterUrl($item['key'], $value['value']).'">x</a></span> ';
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
      if ($keyId === $parameter[0]['id']) {
        foreach ($parameter[1] as $value) {
          if ($valueId === $value['id']) {
            return true;
          }
        }
      }
    }
  }

  private function isSelectedByName($key, $value) {
    foreach ($this->parameters as $parameter) {
      if ($key === $parameter[0]['key']) {
        foreach ($parameter[1] as $value) {
          if ($value === $value['value']) {
            return true;
          }
        }
      }
    }
  }

  private function appendFilterUrl($key, $value) {
    $list = array();
    $isInserted = false;
    foreach ($this->parameters as $parameter) {
      if ($key === $parameter[0]['key']) {
        $values = $this->getValues($parameter);
        $values[] = urlencode($value);
        $list[] = urlencode($parameter[0]['key']).'='.implode(':', $values);
        $isInserted = true;
        continue;
      }
      $list[] = urlencode($parameter[0]['key']).'='.implode(':', $this->getValues($parameter));
    }
    if (!$isInserted) {
      $list[] = urlencode($key).'='.urlencode($value);
    }
    if (count($list) !== 0) {
      return '?'.implode('&', $list);
    }
  }

  private function removeFilterUrl($key, $value) {
    $list = array();
    foreach ($this->parameters as $parameter) {
      if ($key === $parameter[0]['key']) {
        $values = $this->getValues($parameter, $value);
        if (count($values) !== 0) {
          $list[] = urlencode($parameter[0]['key']).'='.implode(':', $values);
        }
        continue;
      }
      $list[] = urlencode($parameter[0]['key']).'='.implode(':', $this->getValues($parameter, $value));
    }
    if (count($list) !== 0) {
      return '?'.implode('&', $list);
    }
    return '.';
  }

  private function getValues($parameter, $removeValue = null) {
    $result = array();
    foreach ($parameter[1] as $value) {
      if ($removeValue !== $value['value']) {
        $result[] = urlencode($value['value']);
      }
    }
    return $result;
  }
}