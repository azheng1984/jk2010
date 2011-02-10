<?php
class ArgumentVerifier {
  public function run($reflector, $length, $isInfinite) {
    foreach ($reflector->getParameters() as $parameter) {
      if ($parameter->isOptional() && $length == 0) {
        break;
      }
      --$length;
    }
    if ($length < 0) {
      throw new Exception;
    }
    if ($length > 0 && $isInfinite == false) {
      throw new Exception;
    }
  }
}