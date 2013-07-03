<?php
class TestException extends Exception {
    public function __toString() {
        return __CLASS__;
    }
}
