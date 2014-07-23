<?php
namespace Hyperframework\Web\Html;

class FormFilter {
    public static function run($config) {
        //preprocess config
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
}
