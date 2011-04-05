<?php
class CommandContext {
  private static $options = array();

  public static function addOption($name, $value) {
    self::$options[$name] = $value;
  }

  public static function getOption($name) {
    if (self::hasOption($name)) {
      return self::$options[$name];
    }
  }

  public static function hasOption($name) {
    return isset(self::$options[$name]);
  }
}