<?php
class ApplicationException extends Exception
{
  #for < 5.3
  private static $chain;

  private $status;

  public static function getChain()
  {
    return self::$chain;
  }

  public function __construct($message, IStatus $status)
  {
    Exception::__construct($message);
    if (self::$chain == null) {
      self::$chain = array();
    }
    self::$chain[] = $this;
    $this->status = $status;
  }

  public function getStatus()
  {
    return $this->status;
  }

  public function __toString()
  {
    $result = '';
    foreach (self::$chain as $exception) {
      $result .= $exception->toString()."\n";
      $result .= "Application Status: ".$exception->status->getCode().' '.$exception->status->getName()."\n\n";
    }
    return $result;
  }

  private function toString()
  {
    return parent::__toString();
  }
}