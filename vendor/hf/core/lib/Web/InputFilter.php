<?php
namespace Hyperframework\Web;

class InputFilter {
    private static $instances = array();
    private static $configs = array();

    public static function getInstance($name) {
        //load field & global config from config file using data loader
    }

    public static function addConfig($fieldConfig, $globalConfig) {
    }

    public function __construct($fieldConfig, $globalConfig = null) {
        //config load from config and share with asset/js
        //config should be shared with client controller - js, not client model
    }

    public function isValid() {
    }

    public function getResult() {
        //if invalid, throw ValidationException
    }

    public function getErrors() {
    }

    public function getInput() {
        //will not throw any exception, just extract input value from get/post/url/cookie/session
    }
}
