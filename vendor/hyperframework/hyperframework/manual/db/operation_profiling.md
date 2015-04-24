# 操作剖析
可以通过把配置 hyperframework.db.operation_profiler.enable 设置成 true 来开启操作剖析器（默认关闭）。
## 记录日志
当日志剖析器被开启时，默认会通过 Hyperframework\Logging\Logger 来输出操作日志。可以通过配置关闭或者修改 logger 类。

## 定制化剖析数据处理
可以通过配置 hyperframework.db.operation_profiler.profile_handler_class 设置剖析数据处理器类，剖析数据处理器类必须实现 handle 方法来接收数据，例如：
```.php
class ProfilerHandler {
    public function handle(array $profile) {
        //...
    }
}
```
