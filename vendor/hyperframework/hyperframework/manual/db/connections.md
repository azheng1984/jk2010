# 连接
## 简介
Hyperframework\Db\DbConnection 继承自 PDO 类。

可以通过 getName 方法获取连接的名称，默认名称是 default。

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
也可已配置多个连接，例如：
```.php
<?php
return [
    'db1' => [
        'dsn' => 'mysql:host=localhost;dbname=db1',
        'username' => 'test',
        'password' => 'test'
    ],
    'db2' => [
        'dsn' => 'mysql:host=localhost;dbname=db2',
        'username' => 'test',
        'password' => 'test'
    ],
];
```
## 连接池
默认每个连接都会被放入连接池，直到脚本运行结束后被释放或者手动关闭。可以通过配置禁用连接池。

## 获取连接
```.php
$connection = Hyperframework\Db\DbClient::getConnection();
```

## 设置连接
```.php
Hyperframework\Db\DbClient::setConnection($connection);
```
## 切换连接
```.php
$name = 'db1';
Hyperframework\Db\DbClient::connect($name);
```
## 关闭连接
```.php
$name = 'db1';
Hyperframework\Db\DbClient::closeConnection($name);
```
$name 参数可选，如果没有 $name 参数，则会关闭当前连接。
