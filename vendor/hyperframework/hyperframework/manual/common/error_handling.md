# 错误处理
## 错误转异常

## 写日志

当 hyperframework.error_handler.enable_logger 配置被设置成 true 的时候（默认为 false）
### 错误码和日志级别的关系
```.php
[
    E_DEPRECATED        => LogLevel::NOTICE,
    E_USER_DEPRECATED   => LogLevel::NOTICE,
    E_STRICT            => LogLevel::NOTICE,
    E_NOTICE            => LogLevel::NOTICE,
    E_USER_NOTICE       => LogLevel::NOTICE,
    E_WARNING           => LogLevel::WARNING,
    E_USER_WARNING      => LogLevel::WARNING,
    E_COMPILE_WARNING   => LogLevel::WARNING,
    E_CORE_WARNING      => LogLevel::WARNING,
    E_RECOVERABLE_ERROR => LogLevel::FATAL,
    E_USER_ERROR        => LogLevel::FATAL,
    E_ERROR             => LogLevel::FATAL,
    E_PARSE             => LogLevel::FATAL,
    E_COMPILE_ERROR     => LogLevel::FATAL,
    E_CORE_ERROR        => LogLevel::FATAL
]
```
## 获取错误对象
```.php
$this->getError();
```

getError 是 protected 方法。
