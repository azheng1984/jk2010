# 注册表基础
## 注册
```.php
Hyperframework\Common\Registry::set('key', $value);
```
## 获取值
```.php
$value = Hyperframework\Common\Registry::get('key');
```
## 移除项
```.php
Hyperframework\Common\Registry::remove('key');
```
## 查询键是否已经存在
```.php
$value = Hyperframework\Common\Registry::has('key');
```
## 清空注册表
```.php
Hyperframework\Common\Registry::clear();
```
