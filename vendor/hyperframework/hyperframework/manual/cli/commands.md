# 命令
## 简介
单命令应用中，默认的命令类是应用根命名空间下的 Command 类，例如，项目的根命名空间是 MyProject，那么命令类默认是 MyProject\Command。

多命令应用中，默认的命令类是应用根命名空间加 Subcommands 子命名空间下的类，类名和子命令名对应，例如，项目的根命名空间是 MyProject，子命令名称是 my-subcommand，那么命令类默认是 MyProject\Subcommands\MySubcommand。

命令对象由 app 类负责创建，并调用 execute 方法执行命令，参数是用户输入的命令参数。

命令类范例：
```.php
using Hyperframework\Cli\Command as Base;

class Command extends Base {
    public function execute($arg) {
        //...
    }
}
```

## 命令参数
获取所有命令参数：
```.php
$args = $this->getArguments();
```
## 命令选项
获取单个命令选项：
```.php
$value = $this->getOption($name);
```
如果 option 不存在，返回 null。如果 option 存在但没有参数，返会 true。

获取所有命令选项：
```.php
$options = $this->getOptions();
```

查询命令选项是否存在：
```.php
$hasOption = $this->hasOption($name);
```
## 获取 App 对象
```.php
$app = $this->getApp();
```
## 退出
```.php
$this->quit();
```
命令的 quit 方法会调用 app 对象的 quit 方法退出应用。
