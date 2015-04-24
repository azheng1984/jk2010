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
```
此时可以通过 DbClient 的 connect 函数切换连接，例如：
```.php
Hyperframework\Db\DbClient::connect('db2');
```

## 执行命令
### 执行
### 插入记录
### 修改记录
### 删除记录
## 查询记录
### 查询
### 查询列
### 查询行
### 查询行集合
## 查询统计数据
## 获取最后插入的 Id
## 准备 DbStatement
## 为标识符添加引号
