# 安全
## CSRF 防御
CSRF 防御默认是开启的，可以通过配置关闭：
```.php
Config::set('hyperframework.web.csrf_protection.enable', false);
```
查询 CSRF 防御是否被开启：
```.php
$isEnabled = Hyperframework\Web\CsrfProtection::isEnabled();
```

**获取 token 名称**

```.php
$tokenName = Hyperframework\Web\CsrfProtection::getTokenName();
```
默认值：_csrf_token

可以通过配置修改：
```.php
Config::set('hyperframework.web.csrf_protection.token_name', 'custom_token_name');
```

**获取 token 值**
```.php
$token = Hyperframework\Web\CsrfProtection::getToken();
```

**修改 CSRF 防御引擎**
```.php
Config::set('hyperframework.web.csrf_protection.engine_class', 'CustomCsrfProtectionEngine');
```
默认值：Hyperframework\Web\CsrfProtectionEngine
