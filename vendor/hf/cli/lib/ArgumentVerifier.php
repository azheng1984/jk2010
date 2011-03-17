<?php
class ArgumentVerifier {
  public function verify($reflector, $length, $isInfinite) {
    $count = $length;
    foreach ($reflector->getParameters() as $parameter) {
      if ($parameter->isOptional() && $count === 0) {
        break;
      }
      --$count;
    }
    if ($count < 0 || ($count > 0 && $isInfinite === false)) {
      $expectationLength = count($reflector->getParameters());
      throw new CommandException(
        'Argument length not matched'.
        "(input:$length expectation:$expectationLength".
        ($isInfinite ? ' or more' : '').
        ')'
      );
    }
  }
}