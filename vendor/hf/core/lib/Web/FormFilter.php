<?php
namespace Hyperframework\Web\Html;

class FormFilter {
    public static function run($config) {
        if (is_string($config)) {
            $config = static::getConfig($config);
        }
        //parse config
        //use fields to extract field
        //use :validation_rules to start validation
        //validation rules also can be inline, also use :validation_rules prefix
        //'hyperframework.web.validation_rules_as_attr = true'
        $result = array();
        foreach ($config as $attrs) {
            $name = $attrs['name'];
            if (isset($_POST[$name])) {
                $result[$name] = $_POST[$name];
            } else {
                $result[$name] = null;
            }
        }
        return $result;
    }

    protected static function loadConfig($name) {
        return FormConfigLoader::run($name);
    }
}
