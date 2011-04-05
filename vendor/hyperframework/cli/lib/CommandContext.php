<?php
class CommandContext {
  private static $options;

  public static function initialize($options) {
    self::$options = $options;
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