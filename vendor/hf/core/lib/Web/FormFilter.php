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
            $query = $ctx->getInput(
                'GET', array('name' => 'query', 'default' => 'hello');
            );
        }
        return $result;

        $articleFormConfig = array(
            ':base' => 'article',
            ':validation_rules' => Article::getValidationRules(),
        )
        FormBuilder::run($articleFormconfig);

        $article = $ctx->getForm('article');
        if (Article::isValid($article, $errors)) {
        }
    }

    protected static function getConfig($name) {
        return FormConfigLoader::run($name);
    }
}
