# 事务
### 开始事务
```.php
Hyperframework\Db\DbClient::beginTransaction();
```
### 递交事务
```.php
Hyperframework\Db\DbClient::commit();
```
### 回滚事务
```.php
Hyperframework\Db\DbClient::rollback();
```
### 查询事务状态
```.php
$status = Hyperframework\Db\DbClient::inTransaction();
```
### 自动化事务处理
可以通过 Hyperframework\Db\DbTransaction 自动处理事务，当没有异常抛出时递交，当有异常抛出时回滚，例如：
```.php
Hyperframework\Db\DbTransaction::run($callback);
```
参数 $callback 必须是一个闭包函数（closure）。
