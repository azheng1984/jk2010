# 多命令应用
## 和单命令应用的区别
多命令应用在初始化时会设置子命令名称和全局选项。

在执行命令时，如果存在子命令则执行 executeSubcommand 方法，如果不存在，则执行 executeGlobalCommand 方法（默认调用 renderHelp 方法渲染全局帮助）。

executeSubcommand 根据子命令类配置，创建子 subcommand 对象。然后，调用 subcommand 对象的 execute 方法（输入参数等于传入 execute 方法参数）。

## 什么是 MultipleCommandApp 类
Cli 模块中的 MultipleCommandApp 类继承自同模块的 App 类，并通过 run 静态方法定义了多命令应用的主流程。应用启动文件（run）通过调用该类中的 run 静态方法来运行应用。

## 子命令配置
**配置文件**

子命令配置文件存放在 config/subcommands 文件夹，文件名必须是子命令名称加 .php 后缀，例如，子命令是 show，那么配置文件名等于 show.php。

**描述**
```.php
[
    'description' => 'content'
];
```
**参数**

和单命令应用命令参数配置相同。

**选项**

和单命令应用命令选项配置相同。

**类**

和单命令应用命令类配置相同。

**互斥选项**

和单命令应用命令互斥选项配置相同。

**全局命令选项**

设置全局命令选项：
```.php
$app->setGlobalOptions($options);
```
参数 $options 是一个数组，包含所有用户输入的选项值，字段名表示 option 的名称，如果 option 没有参数，默认值是 true。setGlobalOptions 是 protected 方法。

获取所有全局命令选项：
```.php
$options = $app->getGlobalOptions();
```
获取单个全局命令选项：
```.php
$value = $app->getGlobalOption($name);
```
查询全局命令选项是否存在：
```.php
$hasOption = $app->hasGlobalOption($name);
```
## 子命令名称
获取：
```.php
$name = $app->getSubcommandName();
```
设置：
```.php
$app->setSubcommandName($name);
```
## 查询用户输入的命令中是否包含子命令
$hasSubcommand = $app->hasSubcommand();

## 其他
由于 MultipleCommandApp 类继承自 Cli 模块的 App 类，通过 [单命令应用](single_command_applications) 获取更多相关信息。
