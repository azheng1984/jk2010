<?php
namespace Hyperframework\Common\Test;

use Hyperframework\Common\ErrorHandler as Base;

class ErrorHandler extends Base {
    public function disableDefaultErrorReportingForTest() {
        $this->disableDefaultErrorReporting();
    }

    public function publicShouldDisplayErrors() {
        return $this->shouldDisplayErrors();
    }

    final protected function getSourceForTest() {
        return $this->getSource();
    }

    final protected function isError() {
        if ($this->source === null) {
            throw new InvalidOperationException('No error or exception.');
        }
        return $this->isError;
    }

    final protected function isLoggerEnabled() {
        return $this->isLoggerEnabled;
    }

    final protected function isDefaultErrorLogEnabled() {
        return $this->isDefaultErrorLogEnabled;
    }

    final protected function getErrorReportingBitmask() {
        return $this->errorReportingBitmask;
    }
}
