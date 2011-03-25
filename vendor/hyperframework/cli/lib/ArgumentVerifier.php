<?php
class ArgumentVerifier {
  public function verify($reflector, $length, $isInfinite) {
    $count = $length;
    $parameters = $reflector->getParameters();
    foreach ($parameters as $parameter) {
      if ($parameter->isOptional() && $count === 0) {
        break;
      }
      --$count;
    }
    if ($count < 0 || ($count > 0 && $isInfinite === false)) {
      $expectation = $this->getExpectation($parameters, $isInfinite);
      throw new CommandException(
        "Argument length is not correct(Actual:$length Expected:$expectation)"
      );
    }
  }

  private function getExpectation($parameters, $isInfinite) {
    $optionalParameterLength = 0;
    foreach ($parameters as $parameter) {
      if ($parameter->isOptional()) {
        ++$optionalParameterLength;
      }
    }
    $parameterLength = count($parameters);
    $result = $parameterLength - $optionalParameterLength;
    if ($optionalParameterLength !== 0) {
      $result .= '-'.$parameterLength;
    }
    if ($isInfinite) {
      $result .= ' or more';
    }
    return $result;
  }
}