# 文件加载器

## 加载 PHP 文件
```.php
$result = Hyperframework\Common\FileLoader::loadPhp($path);
```
如果加载文件是相对路径，那么会基于项目根路径。

## 加载数据文件
```.php
$result = Hyperframework\Common\FileLoader::loadData($path);
```
如果加载文件是相对路径，那么会基于项目根路径。

## 加载 PHP 配置文件
```.php
$result = Hyperframework\Common\ConfigFileLoader::loadPhp($path);
```
如果加载文件是相对路径，那么会基于项目根路径的 config 文件夹。

## 加载配置数据文件
```.php
$result = Hyperframework\Common\ConfigFileLoader::loadData($path);
```
如果加载文件是相对路径，那么会基于项目根路径的 config 文件夹。

## 加载 PHP 缓存文件
```.php
$result = Hyperframework\Common\CacheFileLoader::loadPhp($path);
```
如果加载文件是相对路径，那么会基于项目根路径的 cache 文件夹。

## 加载缓存数据文件
```.php
$result = Hyperframework\Common\CacheFileLoader::loadData($path);
```
如果加载文件是相对路径，那么会基于项目根路径的 cache 文件夹。
