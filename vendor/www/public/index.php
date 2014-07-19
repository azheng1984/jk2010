<?php
class index {
    protected static function name() {
        echo 'hello';
    }

    public static function hi() {
        call_user_func('static::name');
    }
    
}
index::hi();
exit;

class index2 extends index {
    public static function name() {parent::name();}
    public static function getValidationRules() {}
}

index2::name();
