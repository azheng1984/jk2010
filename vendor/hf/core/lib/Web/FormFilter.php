<?php
namespace Hyperframework\Web\Html;

class FormFilter {
    public static function execute($config, $source = null) {
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
