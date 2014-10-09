<?php
namespace Hyperframework\Blog\Db;

use Hyperframework\Db\DbTable;
use Hyperframework\Db\DbClient;
use Hyperframework\Blog\Db\DbComment;

final class DbArticle extends DbTable {
    public static function getTopLike() {
        return DbClient::getColumn(
            'SELECT * FROM Article ORDER BY like_count DESC LIMIT 1'
        );
    }

    public static function deleteById($id) {
        DbTransaction::run(function() use ($id) {
            parent::deleteById($id);
            DbComment::deleteByColumns(['article_id' => $id]);
        });
    }
}
