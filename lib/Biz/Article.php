<?php
namespace Hyperframework\Blog\Biz;

use Hyperframework\Db\DbActiveRecord;
use Hyperframework\Db\DbClient;
use Hyperframework\Blog\Biz\Comment;

class Article extends DbActiveRecord {
    public static function getTopLiked() {
       return static::getAllBySql('ORDER BY like_count DESC LIMIT 1');
    }

    public function delete() {
        DbTransaction::run(function() {
            parent::delete();
            DbClient::deleteByColumns('Comment', ['article_id' => $this['id']]);
        });
    }

    public function isPopular() {
        return $this['view_count'] > 10;
    }
}
