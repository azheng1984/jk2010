<?php
class InternalServerErrorException extends ApplicationException
{
  protected $severity;

  public function __construct($message, $code, $severity, $file, $line)
  {
    parent::__construct($message, new InternalServerErrorStatus);
    $this->code = $code;
    $this->severity = $severity;
    $this->file = $file;
    $this->line = $line;
  }

  public function getSeverity()
  {
    return $this->severity;
  }
}