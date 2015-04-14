# App 基础
## 1 App 构造函数
在 App 构造函数中会初始化配置和错误处理器。

### 1.1 初始化配置
App 通过自身的 initializeConfig 初始化配置。

应用必须包含 config/init.php 初始化配置文件，它会在此时被导入。

如果设置了 $_ENV['HYPERFRAMEWORK_ENV']，那么初始化配置时会检查对应的环境配置文件，如果存在就会导入。例如，当 $_ENV['HYPERFRAMEWORK_ENV'] 等于 dev，config/environments/dev.php 就会被导入。

### 1.2 初始化错误处理器
App 通过自身的 initializeErrorHandler 初始化错误处理器。

可以通过配置修改错误处理器类：
```.php
Config::set('hyperframework.error_handler.class', 'CustomErrorHandler');
```

默认的错误处理器类是 Hyperframework\Common\ErrorHandler（App 的子类可以定制默认的错误处理器类，例如，Web 模块的 App 默认使用 Hyperframework\Web\ErrorHandler）。

## 2 退出应用
可以通过调用 App 对象的 quit 方法退出应用。Quit 方法会调用 App 对象的 finalize 方法结束应用逻辑，然后通过 ExitHelper 类的 exitScript 静态方法推出程序。 
