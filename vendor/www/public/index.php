<?php
class index {
    protected static function name() {
        echo 'hi';
    }
}

class index2 extends index {
    public static function name() {parent::name();}
    public static function getValidationRules() {}
}

index2::name();
