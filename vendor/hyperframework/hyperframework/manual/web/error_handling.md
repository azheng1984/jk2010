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

NOTE：如果已经有响应输出，错误视图不会被显式。

## Http 异常
Http 异常可以指定 http 响应状态码和相关头部信息。例如，当 Hyperframework\Web\NotFoundException 异常的抛出时，http 响应状态码会被设置成 404。

Http 异常默认不会被写入错误日志，可以通过配置开启：
```.php
Config::set('hyperframework.web.log_http_exception', true);
```

## Debugger
Debugger 的作用：

1. 分离错误信息和响应输出

2. 分离内部/外部调用堆栈

使用 debugger 需要通过配置开启：
```.php
Config::set('hyperframework.web.debugger.enable', true);
```

NOTE: 当 debugger 开启时，输出会被缓存。

可以通过配置修改 debugger 类：
```.php
Config::set('hyperframework.web.debugger.class', 'CustomDebugger');
```
默认值：Hyperframework\Web\Debugger

可以通过配置限制 debugger 最大输出缓存大小：
```.php
Config::set('hyperframework.web.debugger.max_output_buffer_size', '10m');
```
默认不限制。详细信息参考 [配置](configuration)。

## 其他
由于 Web 模块的 ErrorHandler 类继承自 Common 模块的 ErrorHandler 类，通过 Common 模块文档中的 [错误处理](/cn/manual/common/error_handling) 获取更多相关信息。
