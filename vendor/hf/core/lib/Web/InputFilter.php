<?php
namespace Hyperframework\Web;

class InputFilter {
    public static function run($fields, $source) {
        if (is_array($fields) === false) {
            $fields = array($fields);
        }
        if ($source === null) {
            $source = $_SERVER['REQUEST_METHOD'] === 'GET'
                || $_SERVER['REQUEST_METHOD'] === 'HEAD' ? $_GET : $_POST;
        }

        $query = $ctx->getParam('query', $_GET);
        $query = $ctx->getParam('query', $_COOKIE);

    }

    public static function create($options) {
        //load field & global config from config file using config file loader
    }

    public static function addConfig($fieldConfig, $globalConfig) {
    }

    public function __construct($fieldConfig, $globalConfig = null) {
        //config load from config and share with asset/js
        //config should be shared with client controller - js, not client model
    }

    $input = $app->filter(
        array('user_id' => 'required', 'email' => 'email', 'title'), 'GET'
    );

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
