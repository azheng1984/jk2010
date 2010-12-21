<?php
class ErrorHandler
{
  public static function initialize()
  {
    set_error_handler(array('ErrorHandler', 'handle'));
  }

  public static function handle($number, $explanation, $file, $line)
  {
    throw new InternalServerErrorException($explanation, $number, 0, $file, $line);
  }
}