<?php
class ArgumentVerifier {
  public function verify($reflector, $length, $isInfinite) {
    foreach ($reflector->getParameters() as $parameter) {
      if ($parameter->isOptional() && $length === 0) {
        break;
      }
      --$length;
    }
    if ($length < 0 || ($length > 0 && $isInfinite === false)) {
      throw new CommandException('argument not matched');
    }
  }
}