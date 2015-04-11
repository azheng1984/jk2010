# 路由
## 简介
通过实现 Hyperframework\Web\Router 类的 execute 抽象方法来处理路由逻辑。execute 方法在 Router 的构造函数中被调用。

execute 方法可以返回一个 bool 值，表示路由是否已经匹配。或者返回一个字符串，表示已经匹配，同时设置 module、controller 和 action，格式如下：

```.php
return 'action';
```

或
```.php
return 'controller/action';
```
或
```.php
return 'module/controller/action';
```
也可以超过三层，除了末尾两层，其他都是 module，例如：
```.php
return 'module_segment_1/module_segment_2/controller/action';
```

execute 执行完成后，如果匹配状态等于 false，将会抛出 NotFoundException。

## 规则匹配
### 静态路径
```.php
$this->match('segment');
```

所有匹配规则都建议使用相对路径（默认基于根路径，可以通过 matchScope 修改）。如需匹配顶层路径，则使用 '/'，例如：

```.php
$this->match('/');
```

### 动态段
```.php
$this->match(':segment');
```

如果匹配成功，可以通过 getParam 获取对应的值，例如：

```.php
$this->getParam('segment');
```

:module、:controller 和 :action 动态段会被用来设置 module、controller 和 action，例如：

```.php
$this->match(':module/:controller/:action');
```

### 可选段
```.php
$this->match('required(/optional)');
```

### 通配符
```.php
$this->match('*wildcard');
```

和动态段不同的是，通配符匹配是贪婪匹配，匹配会跨越 "/"。

如果匹配成功，可以通过 getParam 获取对应的值。

### match 选项
#### 限制 http 请求方法
```.php
$this->match('/', ['methods' => ['GET']]);
```
等价与：

```.php
$this->matchGet('/');
```

限制多个 http 请求方法：
```.php
$this->match('/', ['methods' => ['GET', 'POST']]);
```

#### 文件格式
```.php
$this->match('path', ['format' => 'html']);
```

等价与：

```.php
$this->match('path.html');
```

匹配多个格式：
```.php
$this->match('path', ['format' => 'html|json']);
```

匹配任意格式：
```.php
$this->match('path', ['format' => true]);
```

设置默认格式：
```.php
$this->match('path', ['format' => true, 'default_format' => 'html']);
```

#### 限制动态段格式
可以使用正则表达式限制动态段的格式，例如：
```.php
$this->match(':segment', [':segment' => '[a-z]']);
```

#### 附加规则
```.php
$this->match(':segment', ['extra' => function($matches) {
    if ($matches['segment'][0] === 'x') {
        return true;
    }
}]);
```

附加规则返回 bool 值，true 表示通过匹配。

设置多个附加规则：
```.php
$this->match(':segment', ['extra' => [$callback1, $callback2]]);
```

## 资源匹配
```.php
$this->matchResource('sitemap');
```

此时 controller 等于 sitemap， action 对应关系：

| HTTP 方法 | 路径          | action |
| --------- | --------------| ------ |
| GET       | /sitemap      | show   |
| GET       | /sitemap/new  | new    |
| GET       | /sitemap/edit | edit   |
| POST      | /sitemap      | create |
| PUT/PATCH | /sitemap      | update |
| DELETE    | /sitemap      | delete |

### Action 规则定义
```.php
$actions = ['preview'];
```
等价与：
```.php
$actions = ['preview' => [['GET'], 'preview']];
```

第一个元素是字符串（定义单个 Http 请求方法限制）或数组（定义多个 Http 请求方法限制），默认值是 'GET'。

第二个参数是 action 路径，默认和 action 名称相同。action 路径基于资源路径，例如，当资源路径等于 sitemap，action 路径等于 preview，那么请求路径是 sitemap/preview。

action 规则支持 match 选项（methods 选项除外），例如：
```.php
$actions = ['preview' => ['extra' => $callback]];
```

### 资源匹配选项
#### actions
限定 action，例如：
```.php
$this->matchResource('sitemap', ['actions' => ['show']]);
```

#### extra_actions
附加 action，例如：
```.php
$this->matchResource('sitemap', ['extra_actions' => ['preview']]);
```

#### excluded_actions
排除 action，例如：
```.php
$this->matchResource('sitemap', ['excluded_actions' => ['new']]);
```

#### 更多选项
资源匹配选项支持 match 选项，例如：
```.php
$this->matchResource('sitemap', ['extra' => $callback]);
```

## 资源集合匹配
```.php
$this->matchResources('documents');
```

此时 controller 等于 documents， action 对应关系：

| HTTP 方法 | 路径                | action  |
| --------- | ------------------- | ------ |
| GET       | /documents          | index  |
| GET       | /documents/:id      | show   |
| GET       | /documents/new      | new    |
| GET       | /documents/:id/edit | edit   |
| POST      | /documents          | create |
| PUT/PATCH | /documents/:id      | update |
| DELETE    | /documents/:id      | delete |

#### actions

#### collection_actions

#### element_actions

#### extra_collection_actions

#### extra_element_actions

#### excluded_actions

#### id
默认值 \d+

#### 更多选项
资源集合匹配选项支持 match 选项（methods 选项除外），例如：
```.php
$this->matchResources('documents', ['extra' => $callback]);
```

## 匹配 Scope

## 获取 App 对象
## 获取请求路径
## 设置/获取 Action 方法
## 设置/获取 Action
## 设置/获取 Controller
## 设置/获取 Controller 类
## 设置/获取 Module
## 路由参数
## 重定向
## 查询是否匹配
## 设置/获取匹配状态
