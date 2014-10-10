<?php
namespace Hyperframework\Blog\Biz;

use Hyperframework\Db\DbTable;
use Hyperframework\Db\DbClient;
use Hyperframework\Blog\Biz\Comment;

class Article extends DbTable {
    public static function getTopLike() {
        $client = static::getClient();
        return $client->getColumn(
            'SELECT * FROM Article ORDER BY like_count DESC LIMIT 1'
        );
        DbClient::getColumn(
            'SELECT * FROM Article ORDER BY like_count DESC LIMIT 1'
        );
    }

    public static function delete($article) {
        DbTransaction::run(function() use ($article) {
            parent::delete($row);
            static::getClient()->deleteByColumns('Comment', ['article_id' => $row['id']]);
            DbClient::deleteByColumns('Comment', ['article_id' => $row['id']]);
        });
    }

    public static function isPopular($article) {
    }
}
