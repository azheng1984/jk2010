<?php
namespace Hyperframework\Web;

class InputMapper {
    public function __construct($config) {
        //config load from config and share with asset/js
        //config should be shared with client controller - js, not client model
    }

    public function getResult() {
        //if invalid, throw ValidationException
    }

    public function getInput() {
        //will not throw any exception, just extract input value from request/url/cookie/session
    }

    public function getErrors() {
    }

    public function isValid() {
    }
}
