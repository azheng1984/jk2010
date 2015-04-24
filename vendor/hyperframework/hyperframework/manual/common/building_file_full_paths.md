# 构建文件全路径
## 基于项目根路径
```.php
$fullPath = Hyperframework\Common\FileFullPathBuilder::build($relativePath);
```
## 基于配置根路径
```.php
$fullPath = Hyperframework\Common\ConfigFileFullPathBuilder::build($relativePath);
```
## 基于缓存根路径
```.php
$fullPath = Hyperframework\Common\CacheFileFullPathBuilder::build($relativePath);
```
