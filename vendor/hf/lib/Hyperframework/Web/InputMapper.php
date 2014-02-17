<?php
namespace Hyperframework\Web;

//new InputMapper(array('#source' => 'GET', '#name' => 'article', '#validation' => ''));
//$inputMapper = InputMapper::create('basic', array(), 'GET');
//Html::beginInputMapperBinding('article');
//$inputMapper = new FormInput(
//    array('list'),
//    array('source' => 'GET', 'name' => 'article')
//);

//$someCode = array();
////$articleInputMapper = new InputMapper($config1, $config2);

//$articleInputMapper = InputMapper::getInstance('article');
//if ($articleInputMapper->isValid()) {
//
//}

//Html::beginBindingByInputMapper('article');
//$someCode = array();
//Html::endBinding();

//    /article-field.config.php
//    /article-global.config.php

array(
    'fields' => array(
    )
);

//  /config/input_mapper/article.config.php
//  /config/input_mapper/search.config.php 
//return array('source' => 'GET', 'fields' => array(
//    'hi1' => array(
//        'max_length' => 25,
//        'min_length' => 5,
//    ),
//    'hi2' => ''
//));

return array(
    'source' => 'GET',
    'fields' => require __DIR__ . DIRECTORY_SEPARATOR . 'article_fields.php'
);

class InputMapper {
    private static $instances = array();

    public function __construct($fieldConfig, $globalConfig = null) {
        //config load from config and share with asset/js
        //config should be shared with client controller - js, not client model
    }

    public function getResult() {
        //if invalid, throw ValidationException
    }

    public static function getInstance($name) {
        //load field & global config from config file using data loader
    }

    public function getInput() {
        //will not throw any exception, just extract input value from request/url/cookie/session
    }

    public function getErrors() {
    }

    public function isValid() {
    }
}
