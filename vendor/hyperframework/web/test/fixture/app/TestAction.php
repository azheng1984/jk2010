<?php
class TestAction {
    public function GET() {
        $GLOBALS['TEST_CALLBACK_TRACE'][] = __CLASS__ . '->' . __FUNCTION__;
    }
}
