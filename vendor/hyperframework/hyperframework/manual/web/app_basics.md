# App 基础

## 1 什么是 App 类
Web 模块中的 App 类继承自 Common 模块的 App 类，并通过 run 静态方法定义了 Web 应用的主流程。入口文件（public/index.php）通过调用该类中的 run 静态方法来运行应用。

## 2 Web 应用的主流程
### 2.1 App 对象创建
App 类通过调用自身的 createApp 静态方法创建 App 对象。对象创建过程中会执行父类的构造函数，同时重写 Http 方法（可配置），执行 Csrf 检查（可配置）。

### 2.2 Controller 对象创建
通过 Router 获取 Controller 类，并创建 Controller 对象。

### 2.3 运行 Controller
通过调用 Controller 对象的 run 方法来运行 Controller。

### 2.4 结束运行
结束运行时，App 对象的 finalize 方法会被调用。

## 3 获取路由器对象
通过调用 App 对象的 getRouter 方法来获取路由器对象。

可以通过配置修改路由类：
```.php
Config::set('hyperframework.web.router_class', 'CustomRouter');
```

## 4 重写 Http 请求方法
App 通过自身的 rewriteRequestMethod 方法来重写 Http 请求方法。 该方法使用 $_SERVER\['HTTP_X_HTTP_METHOD_OVERRIDE'] 或 $_POST\['_method'] 来重写 $_SERVER\['REQUEST_METHOD']，$_SERVER\['HTTP_X_HTTP_METHOD_OVERRIDE'] 优先级高于 $_POST\['_method']。如果重写成功，$_SERVER\['REQUEST_METHOD'] 的原始值将会保存在 $_SERVER\['ORIGINAL_REQUEST_METHOD'] 中。

可以通过配置关闭 Http 方法重写：
```.php
Config::set('hyperframework.web.rewrite_request_method', false);
```

## 5 执行 Csrf 检查
App 通过自身的 checkCsrf 方法来执行 Csrf 检查。

关于 Csrf 检查的详细信息，参考 [安全](/cn/manual/web/security)。

## 6 默认的错误处理器
Web 模块的 App 使用 Hyperframework\Web\ErrorHandler 作为默认错误处理器。

关于错误处理器初始化的详细信息，参考 Common 模块文档中的 [App 基础](/cn/manual/common/app_basics)。

关于 Web 错误处理的详细信息，参考 [错误处理](/cn/manual/web/error_handling)。

## 7 其他
由于 Web 模块的 App 类继承自 Common 模块的 App 类，通过 Common 模块文档中的 [App 基础](/cn/manual/common/app_basics) 获取更多相关信息。
