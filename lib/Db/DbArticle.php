<?php
namespace Hyperframework\Blog\Db;

use Hyperframework\Db\DbTable;
use Hyperframework\Db\DbClient;
use Hyperframework\Blog\Db\DbComment;

final class DbArticle extends DbTable {
    public static function getTopLike() {
        $client = static::getClient();
        return $client->getColumn(
            'SELECT * FROM Article ORDER BY like_count DESC LIMIT 1'
        );
        DbClient::getColumn(
            'SELECT * FROM Article ORDER BY like_count DESC LIMIT 1'
        );
    }

    public static function delete($row) {
        DbTransaction::run(function() use ($row) {
            parent::delete($row);
            static::getClient()->deleteByColumns('Comment', ['article_id' => $row['id']]);
            DbClient::deleteByColumns('Comment', ['article_id' => $row['id']]);
        });
    }
}
