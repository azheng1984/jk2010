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

class InputHelper {
    protected static function save($name) {
        $inputFilter = new InputFilter($name);
        $result = $inputFilter->execute();
        Db::save($name, $result);
    }
}

post
self::save();

update
self::save();

private function save() {
    try {
        Db::save('article', '');
        InputFilter::save('article');
    } catch (Exception $ex) {
    }
}

DbArticle::delete($id);
DbArticle::getRow($id);
