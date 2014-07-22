<?php
namespace Hyperframework\Blog\Models;

use Hyperframework\Db\DbClient;
use Hyperframework\Validator;
use Hyperframework\Blog\Models\Comment;

final class Article extends \Hyperframework\Db\DbModel {
    private static $validationRules;

    public static function isValid($row, &$errors) {
        return Validator::run(self::getValidationRules(), $row, $errors);
    }

    public static function getValidationRules() {
        if (self::$validationRules === null) {
            self::$validationRules = [];
        }
        return self::$validatonRules;
    }

    public static function getCount() {
        return DbClient::getColumn('SELECT COUNT(*) FROM Article');
    }

    public static function getTopLike() {
        return DbClient::getColumn(
            'SELECT * FROM Article ORDER BY like_count DESC LIMIT 1'
        );
    }

    public static function deleteById($id) {
        DbTransaction::run(function() use ($id) {
            parent::deleteById($id);
            Comment::deleteByColumns(['article_id' => $id]);
        });
    }
}
