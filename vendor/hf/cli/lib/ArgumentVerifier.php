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
      $message = $this->getErrorMessage($reflector, $length, $isInfinite);
      throw new CommandException($message);
    }
  }

  private function getErrorMessage($reflector, $length, $isInfinite) {
    $expectLength = count($reflector->getParameters());
    $moreThan = $isInfinite ? 'more than ' : '';
    return "argument length not matched(current:$length except:$moreThan$expectLength)";
  }
}