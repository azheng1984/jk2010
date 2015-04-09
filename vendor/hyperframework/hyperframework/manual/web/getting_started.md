# 入门

## 通过 Composer 安装 hyperframework
参考 [安装](/cn/manual/web/installation)。

目录结构
/vendor/hyperframework
/vendor/composer

## 创建应用初始配置文件
config/init.php

```.php
<?php
return [];
```

NOTE: init.php 必须返回一个数组。

## 创建应用启动文件
public/index.php

添加应用启动代码

```.php
<?php
use Hyperframework\Web\App.php
require dirname(__DIR__) .  'vendor' . DIRECTORY_SEPERATOR . 'autoload.php';
App::run();
```

## 创建路由器
lib/Router.php

添加路由器代码

```.php
<?php
use Hyperframework\Web\Router as Base;

class Router extends Base {
    protected function execute() {
        if ($this->match('/')) return;
    }
}
```

## 创建控制器
lib/Controllers/IndexController.php

添加控制器代码

## 创建视图
views/index/show.php

添加视图内容

hello world!

## 完成

