<?php
namespace Hyperframework\Web;

//new InputMapper(array('#source' => 'GET', '#name' => 'article', '#validation' => ''));
//$inputMapper = InputMapper::create('basic', array(), 'GET');
//Html::beginInputMapperBinding('article');
//$inputMapper = new FormInput(
//    array('list'),
//    array('source' => 'GET', 'name' => 'article')
//);

$someCode = array();
$articleInputMapper = new InputMapper($config1, $config2);
$articleInputMapper = InputMapper::getInstance('article');
InputMapper::configSource();
Html::beginBindingByInputMapper('article');
$someCode = array();
Html::endBinding();

class InputMapper {
    private static $instances = array();

    private function __construct($fieldConfig, $globalConfig = null) {}

    public function getResult() {
        //if invalid, throw ValidationException
    }

    public static function create($fieldConfig, $globalConfig = null) {
    }

    public static function getInstance($name) {
        //config load from config and share with asset/js
        //config should be shared with client controller - js, not client model
    }

    public function getInput() {
        //will not throw any exception, just extract input value from request/url/cookie/session
    }

    public function getErrors() {
    }

    public function isValid() {
    }
}
