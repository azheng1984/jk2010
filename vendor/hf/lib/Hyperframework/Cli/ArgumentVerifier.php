<?php
namespace Hyperframework\Cli;

class ArgumentVerifier {
    public function verify($reflector, $length, $isInfinite) {
        $parameters = $this->getParameters($reflector);
        $minimum = $this->getMinimum($parameters);
        $maximum = count($parameters);
        if ($length < $minimum || ($length > $maximum && $isInfinite === false)) {
            $expectation = $this->getExpectation($minimum, $maximum, $isInfinite);
            throw new CommandException(
                "Argument length error(expected:$expectation actual:$length)"
            );
        }
    }

    private function getParameters($reflector) {
        if ($reflector !== null) {
            return $reflector->getParameters();
        }
        return array();
    }

    private function getMinimum($parameters) {
        $minimum = 0;
        foreach ($parameters as $parameter) {
            if ($parameter->isOptional()) {
                break;
            }
            ++$minimum;
        }
        return $minimum;
    }

    private function getExpectation($minimum, $maximum, $isInfinite) {
        if ($isInfinite) {
            $maximum = '...';
        }
        if ($minimum === $maximum) {
            return $minimum;
        }
        return $minimum.'-'.$maximum;
    }
}
