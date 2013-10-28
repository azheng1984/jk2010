<?php
class TestScreen {
    public function render() {
        $GLOBALS['TEST_CALLBACK_TRACE'][] = __CLASS__ . '->' . __FUNCTION__;
    }
}
