<?php
namespace Hyperframework\Db;

abstract class DbCacheableTable extends DbTable {
    public static function getCacheById($id, $mixed = null) {
    }

    public static function deleteCacheById($id) {
        //默认采用 memcached 同时保证最终一致性(缓存 3600s)
        //如果要强缓存所有更新够需要开启事务，应该先插入 cache delete 表，等事务结束后，再删除缓存，同时删除 delete 记录，还需要驻守进程来确保缓存清除(事务必须是 read committed 以上级别，否则会导致幻读)，同时表不能重复
    }

    protected static function getCacheExpirationTimeSpan() {
        return 3600;//configurable
    }
}
