<?php
class ArgumentVerifier {
  public function run($reflector, $length, $isInfinite) {
    foreach ($reflector->getParameters() as $parameter) {
      if ($parameter->isOptional() && $length === 0) {
        break;
      }
      --$length;
    }
    if ($length < 0) {
      return false;
    }
    if ($length > 0 && $isInfinite === false) {
      return false;
    }
    return true;
  }
}