<?php
namespace Hft\Db;

class DbArticle extends DbTable {
    protected static function getTableName() {
        return 'article';
    }

    public static function saveForm($config) {
    }
}
