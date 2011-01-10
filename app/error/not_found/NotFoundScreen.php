<?php
class NotFoundScreen
{
  public function render()
  {
    header('Content-Type: text/plain');
    var_dump(ErrorDocument::getException());
  }
}