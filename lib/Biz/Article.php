<?php
namespace Hyperframework\Blog\Biz;

use Hyperframework\Db\DbTable;
use Hyperframework\Db\DbClient;
use Hyperframework\Blog\Biz\Comment;

class Article extends DbTable {
    public static function getTopLike() {
        DbClient::getColumn(
            'SELECT * FROM Article ORDER BY like_count DESC LIMIT 1'
        );
    }

    public static function delete($row) {
        DbTransaction::run(function() use ($row) {
            parent::delete($row);
            DbClient::deleteByColumns(
                'Comment', ['article_id' => $row['id']]
            );
        });
    }

    public static function isPopular($row) {
    }
}
