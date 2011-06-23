<?php
class Database {
  protected function getFilter($key, $value, $isNumeric = true) {
    if ($value === null) {
      return "`$key` is null";
    }
    if ($isNumeric) {
      return "`$key`=$value";
    }
    return "`$key`='$value'";
  }
}