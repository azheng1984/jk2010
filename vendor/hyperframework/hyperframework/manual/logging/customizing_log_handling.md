# 定制化日志处理
## 定制 Formatter
通过配置 hyperframework.logging.log_formatter_class 修改 formatter 类。formatter 类必须实现 format 方法，用于处理日志记录，此函数返回格式化后的日志字符串，例如：
```.php
class Formatter {
    public function format($logRecord) {
        //...
        return $result;
    }
}
```
## 定制 Writer
通过配置 hyperframework.logging.log_writer_class 修改 write 类。write 类必须实现 write 方法，用于处理日志字符串，例如：
```.php
class Writer {
    public function write($text) {
        //...
    }
}
```
## 高级定制
*定制日志处理器*

通过配置 hyperframework.logging.log_handler_class 修改日志处理器类。处理器类必须实现 handle 方法，用于处理日志记录，例如：
```.php
class Handler {
    public function handle($logRecord) {
        //...
    }
}
```
*定制 logger 引擎*

通过配置 hyperframework.logging.logger_engine_class 修改日志引擎类。