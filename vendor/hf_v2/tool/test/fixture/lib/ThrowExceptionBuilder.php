<?php
class ThrowExceptionBuilder {
  public function build($argument) {
    throw new Exception(__CLASS__.'->'.__FUNCTION__);
  }
}