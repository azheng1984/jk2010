<?php
class ArgumentVerifier {
  private function verifyArguments($reflector, $length, $isInfiniteLength) {
    foreach ($reflector->getParameters() as $parameter) {
      if ($parameter->isOptional() && $length == 0) {
        break;
      }
      --$length;
    }
    if ($length < 0) {
      throw new Exception;
    }
    if ($length > 0 && $isInfiniteLength == false) {
      throw new Exception;
    }
  }
}