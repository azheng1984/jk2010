# 配置
## 路由类
名称: hyperframework.web.router_class

类型: string

默认值: Router

## 开启/关闭 http 请求方法重写
名称: hyperframework.web.rewrite_request_method

类型: bool

默认值: true

## 开启/关闭 Csrf 防护
名称: hyperframework.web.csrf_protection.enable

类型: bool

默认值: true

## Csrf 防护引擎类
名称: hyperframework.web.csrf_protection.engine_class

类型: string

默认值: Hyperframework\Web\CsrfProtectionEngine

## Csrf 防护 token 名称
名称: hyperframework.web.csrf_protection.token_name

类型: string

默认值: _csrf_token

## 视图文件根路径
名称: hyperframework.web.view.root_path

类型: string

默认值: views

## 视图文件名是否包含输出格式
名称:hyperframework.web.view.path.include_output_format

类型: bool

默认值: true

## 视图默认输出格式
名称: hyperframework.web.view.default_output_format

类型: string

默认值: html

## 视图文件格式
名称: hyperframework.web.view.format

类型: string

默认值: php

## 视图类
名称: hyperframework.web.view.class

类型: string

默认值: Hyperframework\Web\View

## 错误视图根路径
名称: hyperframework.web.error_view.root_path

类型: string

默认值: views/_error

## 错误视图类
名称: hyperframework.web.error_view.class

类型: string

默认值: Hyperframework\Web\ErrorView

## 记录 http 异常日志
名称: hyperframework.web.log_http_exception

类型: bool

默认值: false

## 开启/关闭错误处理器的 debug 模式
名称: hyperframework.web.debugger.enable

类型: bool

默认值: false

## Debugger 类
名称: hyperframework.web.debugger.class

类型: string

默认值: Hyperframework\Web\Debugger

## Debugger 最大输出缓存大小
名称: hyperframework.web.debugger.max_output_buffer_size

类型: string

默认值: 无
