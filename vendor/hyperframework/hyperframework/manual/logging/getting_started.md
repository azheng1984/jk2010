# 入门
Logger 类位于 Hyperframework\Logging 命名空间下。
## 记录日志
```.php
Logger::log(LoggerLevel::DEBUG, 'message');
```
日志文件默认是项目根目录的 log/app.log（可以通过配置修改）。
 
可以指定日志的时间，比如：
```.php
Logger::log(LoggerLevel::DEBUG, ['message' => 'message', 'time' => time()]);
```
time 字段可以是整数（Unix 时间戳），浮点数，精确到 6 为小数（单位：秒），也可以是一个 DateTime 对象。

可以通过 debug、info、notice、warn、error、fatal 函数来记录日志，比如：
```.php
Logger::debug('message');
```

如果需要在满足日志等级阀值时，日志才会被生成，可以使用回调函数，例如：
```.php
Logger::debug(function() {
    //...
});
```

## 日志等级阀值
日志等级阀值用于筛选需要被记录日志，超过阀值的等级的日志会被忽略。

一共有 7 等级（值从 0 到 6）：OFF、FATAL、ERROR、WARNING、NOTICE、INFO、DEBUG。默认值是 INFO。

设置：
```.php
Logger::setLevel(LoggerLevel::DEBUG);
```
也可以通过配置设置：
```.php
Hyperframework\Common\Config::set('hyperframework.logging.log_level', 'DEBUG');
```
获取：
```.php
$level = Logger::getLevel();
```
