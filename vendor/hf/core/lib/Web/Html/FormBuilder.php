<?php
namespace Hyperframework\Web\Html;

use Hyperframework\ConfigFileLoader;

class FormBuilder {
    public static function run($data, $config) {
        //parse config
        $formHelper = new FormHelper($data, $config);
        if (isset($config['base'])) {
            //include extenconfig
            ConfigFileLoader::loadPhp('form/article.php');
        }
        $formHelper->begin();
        foreach ($config['fields'] as $name => $attrs) {
            $type = $attrs['type'];
            unset($attrs['type']);
            array_unshift($attrs, $name);
            if (method_exists($formHelper, 'render' . $type) === false) {
                throw new \Exception;
            }
            call_user_func_array(
                array($formHelper, 'render' . $type), $attrs
            );
        }
        $formHelper->end();
    }
}
