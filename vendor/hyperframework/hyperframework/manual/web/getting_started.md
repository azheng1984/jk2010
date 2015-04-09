# 入门

## 通过 Composer 安装 Hyperframework Web 模块
参考 [安装](/cn/manual/web/installation)。

## 配置类自动加载
创建 lib 文件夹，同时修改 composer.json，加入 namespace 对应关系：

```.json
{
   "require": {
       "hyperframework/web": "*"
   },
   "autoload": {
        "psr-4": {
            "": "lib"
        }
    }
}
```

为了更新 composer 类加载逻辑，需要在项目根目录中运行：

```.bash
./composer.phar update
```

## 创建应用初始化配置文件
创建 config/init.php，添加配置代码：

```.php
<?php
return [];
```

NOTE: init.php 必须返回一个数组。

## 创建应用启动文件
创建 public/index.php，添加应用启动代码：

```.php
<?php
require dirname(__DIR__) . 'vendor' . DIRECTORY_SEPERATOR . 'autoload.php';
Hyperframework\Web\App::run();
```

## 创建路由器
创建 lib/Router.php，添加路由器代码：

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
创建 lib/Controllers/IndexController.php，添加控制器代码：

```.php
<?php
use Hyperframework\Web\Controller;

class IndexController extends Controller {
    public function doShowAction() {
        return ['message' => 'hello world!'];
    }
}
```

## 创建视图
创建 views/index/show.php，添加视图代码：

```.php
<?php
echo $this['message'];
```

## 完成
访问网站根目录，浏览器将会输出 “hello world!”。
