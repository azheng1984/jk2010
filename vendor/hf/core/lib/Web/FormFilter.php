<?php
namespace Hyperframework\Web\Html;

class FormFilter {
    public static function run($config) {
        //parse config
        //use fields to extract field
        //use :validation_rules to start validation
        //validation rules also can be inline, also use :validation_rules prefix
        'title' => array(
            'id' => true,
            'method' => 'GET',
            'action' => '../list',
            ':type' => 'Text',
            ':fields' => array(
            ),
            ':validation_rules' => array('min' => 100)
        );
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
