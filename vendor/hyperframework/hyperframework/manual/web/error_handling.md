# 错误处理
## 错误视图

错误视图默认存放在视图文件夹的 _error 子文件夹中。

可以通过配置修改错误视图根路径：
```.php
Config::set('hyperframework.web.error_view.root_path', 'error_views');
```

状态码对应的错误视图将会优先使用，例如，同时存在 error.php 和 404.php，响应状态是 404，那么 404.php 就会被使用。

当错误视图不存在时，默认通过纯文本方式显示错误。

错误视图模型包含以下字段：

|    字段     |       说明     |
| ----------- | -------------- |
| status_code | 状态码         |
| status_text | 状态文本       |
| error       | 异常或错误对象 |


可以通过配置修改错误视图类：
```.php
Config::set('hyperframework.web.error_view.class', 'CustomErrorView');
```
 默认值：Hyperframework\Web\ErrorView

NOTE：当已经有响应输出时，错误视图不会被显式。

## Http 异常
Http 异常可以指定 Http 响应的状态码和相关头部信息。例如，当 Hyperframework\Web\NotFoundException 异常的抛出时，http 响应的状态码会被设置成 404。 

Http 异常默认不会被写入错误日志，可以通过配置开启：
```.php
Config::set('hyperframework.error_handler.log_http_exception', true);
```

## Debugger
Debugger 的作用：

1. 分离错误信息和响应输出

2. 分离内部/外部堆栈

使用 debugger 需要使用配置开启错误处理器的 debug：
```.php
Config::set('hyperframework.error_handler.debug', true);
```

NOTE: 当错误处理器的 debug 开启时，输出会被缓存。

可以通过配置修改 debugger 类：
```.php
Config::set('hyperframework.error_handler.debugger_class', 'CustomDebugger');
```
 默认值：Hyperframework\Web\Debugger
