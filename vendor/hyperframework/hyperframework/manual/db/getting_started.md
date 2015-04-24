# 入门
## 连接配置
```.php
<?php
return [
    'dsn' => 'mysql:host=localhost;dbname=db',
    'username' => 'test',
    'password' => 'test',
    'options' => [PDO::ATTR_EMULATE_PREPARES => false]
];
```
更多连接相关的信息参考 [连接](connections);
## 执行命令
### 执行
```.php
$rowCount = Hyperframework\Db\DbClient::execute($sql, $params);
```
返回受影响的行数。

### 插入记录
```.php
Hyperframework\Db\DbClient::insert($table, $row);
```
$row 参数是键值对，键表示列名，值表示列的值。

### 修改记录
```.php
$table = '';
$rowCount = Hyperframework\Db\DbClient::update($table, $columns, $where, $params);
```
参数 $params 可选，返回受影响的行数。

### 删除记录
```.php
$table = 'Article';
$columns = ['title' => 'new title'];
$where = "id = ?";
$params = [1];
$rowCount = Hyperframework\Db\DbClient::delete($table, $where, $params);
```
参数 $params 可选，返回受影响的行数。

根据 id 删除记录：
```.php
$table = 'Article';
$id = 1;
$rowCount = Hyperframework\Db\DbClient::deleteById($table, $id);
```
返回受影响的行数。
## 查询记录
### 查询
```.php
$sql = 'SELECT * FROM Article WHERE id = ?';
$params = [1];
$statement = Hyperframework\Db\DbClient::find($sql, $params);
```
参数 $params 可选，返回 Hyperframework\Db\DbStatement 对象。

### 查询列
```.php
$sql = 'SELECT name FROM Article WHERE id = ?';
$params = [1];
$value = Hyperframework\Db\DbClient::findColumn($sql, $params);
```
参数 $params 可选，返回列的值。

```.php
$sql = 'SELECT * FROM Article WHERE id = ?';
$params = [1];
$row = Hyperframework\Db\DbClient::findRow($sql, $params);
```
参数 $params 可选，返回行。

### 查询多行
```.php
$sql = 'SELECT * FROM Article WHERE id > ?';
$params = [1];
$rows = Hyperframework\Db\DbClient::findAll($sql, $params);
```
参数 $params 可选，返回行数组。

## 查询统计数据
### count
```.php
$table = 'Article';
$where = 'id < ?';
$params = [100];
$count = Hyperframework\Db\DbClient::count($table, $where, $params);
```
参数 $where 和 $params 可选，返回计数值。
### min
```.php
$table = 'Article';
$where = 'id < ?';
$columnName = 'view_count';
$params = [100];
$min = Hyperframework\Db\DbClient::min($table, $columnName, $where, $params);
```
参数 $where 和 $params 可选，返回最小值。
### max
```.php
$table = 'Article';
$where = 'id < ?';
$columnName = 'view_count';
$params = [100];
$max = Hyperframework\Db\DbClient::max($table, $columnName, $where, $params);
```
参数 $where 和 $params 可选，返回最大值。
### sum
```.php
$table = 'Article';
$where = 'id < ?';
$columnName = 'view_count';
$params = [100];
$sum = Hyperframework\Db\DbClient::sum($table, $columnName, $where, $params);
```
参数 $where 和 $params 可选，返回总和。
### average
```.php
$table = 'Article';
$where = 'id < ?';
$params = [100];
$average = Hyperframework\Db\DbClient::average($table, $columnName, $where, $params);
```
参数 $where 和 $params 可选，返回均值。
## 获取最后插入的 id
```.php
$id = Hyperframework\Db\DbClient::getLastInsertId();
```
## 准备 DbStatement
```.php
$driverOptions = [];
$statement = Hyperframework\Db\DbClient::prepare($sql, $driverOptions);
```
$driverOptions 可选（和 PDO::prepare 函数中的 $driverOptions 参数用法相同），返回 Hyperframework\Db\DbStatement 对象。

## 为标识符添加引号
```.php
$quotedIdentifier = Hyperframework\Db\DbClient::quoteIdentifier($identifier);
```
