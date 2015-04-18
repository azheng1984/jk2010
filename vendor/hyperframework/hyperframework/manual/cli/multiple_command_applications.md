# 多命令应用
## 和单命令应用的区别
多命令应用的主流程和单命令的区别是多命令应用会初始化子命令名称和全局选项，如果存在子命令则执行 executeSubcommand 方法，如果不存在，则执行 executeGlobalCommand 方法（默认渲染全局帮助）
## 什么是 MultipleCommandApp 类
Cli 模块中的 MultipleCommandApp 类继承自同模块的 App 类，并通过 run 静态方法定义了多命令应用的主流程。入口文件（run）通过调用该类中的 run 静态方法来运行应用。

## 子命令执行
根据子命令类配置，创建子命令对象。然后，调用子命令对象的 execute 方法（输入参数等于传入 execute 方法参数）。

## 子命令配置
### 描述
### 参数
### 选项
### 类
### 互斥选项
## 全局命令
## 获取/设置全局选项
## 获取/设置子命令名称
## 查询子命令是否存在
## 其他
由于 MultipleCommandApp 类继承自 Cli 模块的 App 类，通过 [单命令应用](single_command_applications) 获取更多相关信息。
