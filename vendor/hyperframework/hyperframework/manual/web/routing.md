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
超过三层时，除末尾两层外，其他都是 module，例如：
```.php
return 'module_segment_1/module_segment_2/controller/action';
```

execute 执行完成后，如果匹配状态等于 false，同时也没有返回表示匹配成功的值，将会抛出 NotFoundException。

## 规则匹配
### 静态路径
```.php
$this->match('segment');
```

匹配规则建议使用相对路径（默认基于根路径，可以通过 matchScope 修改）。如需匹配顶层路径，则使用 '/'，例如：

```.php
$this->match('/');
```

### 动态段
```.php
$this->match(':segment');
```

如果匹配成功，可以通过 Router 的 getParam 获取对应的值，例如：

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

如果匹配成功，可以通过 Router 的 getParam 获取对应的值。

### match 选项
#### 限制 Http 请求方法
```.php
$this->match('/', ['methods' => ['GET']]);
```
等价与：

```.php
$this->matchGet('/');
```

限制多个 Http 请求方法：
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

会使用预定义 action 规则：

| Http 方法 | 路径          | action |
| --------- | --------------| ------ |
| GET       | /sitemap      | show   |
| GET       | /sitemap/new  | new    |
| GET       | /sitemap/edit | edit   |
| POST      | /sitemap      | create |
| PUT/PATCH | /sitemap      | update |
| DELETE    | /sitemap      | delete |

controller 等于 sitemap。

### 自定义 action
```.php
$actions = ['preview'];
```
等价与：
```.php
$actions = ['preview' => [['GET'], 'preview']];
```

第一个元素可以是字符串（定义单个 Http 请求方法限制）或数组（定义多个 Http 请求方法限制），默认值是 'GET'。

第二个参数是 action 路径，默认和 action 名称相同。action 路径基于资源路径，例如，当资源路径等于 sitemap，action 路径等于 preview，那么请求路径是 sitemap/preview。

action 规则支持 match 选项（methods 选项除外），例如：
```.php
$actions = ['preview' => ['extra' => $callback]];
```

### 使用预定义 action
例如：
```.php
$actions = ['show'];
```

等价与：

```.php
$actions = ['show' => [['GET'], '/']];
```

### 修改预定义 action 规则
```.php
$actions = ['delete' => ['DELETE', 'remove']];
```

### 资源匹配选项
#### actions
设置 action，例如：
```.php
$this->matchResource('sitemap', ['actions' => ['show']]);
```

默认值：`['show', 'new', 'edit', 'create', 'update', 'delete']`

#### 更多选项
资源匹配选项支持 match 选项，例如：
```.php
$this->matchResource('sitemap', ['extra' => $callback]);
```

## 资源集合匹配
```.php
$this->matchResources('documents');
```

会使用预定义集合 action 规则：

| Http 方法 | 路径                | action |
| --------- | ------------------- | ------ |
| GET       | /documents          | index  |
| GET       | /documents/new      | new    |
| POST      | /documents          | create |

和预定义元素 action 规则：

| Http 方法 | 路径                | action |
| --------- | ------------------- | ------ |
| GET       | /documents/:id      | show   |
| GET       | /documents/:id/edit | edit   |
| PUT/PATCH | /documents/:id      | update |
| DELETE    | /documents/:id      | delete |

controller 等于 documents。

#### collection_actions
设置集合 action，例如：
```.php
$this->matchResources('documents', ['collection_actions' => ['index']]);
```
默认值：`['index', 'new', 'create']`

#### element_actions
设置元素 action，例如：
```.php
$this->matchResource('sitemap', ['element_actions' => ['show']]);
```
默认值：`['show', 'edit', 'update', 'delete']`

#### id
通过正则表达式定义元素 id 匹配规则，例如：
```.php
$this->matchResources('documents', ['id' => '[a-z]+']);
```
默认值：\d+

#### 更多选项
资源集合匹配选项支持 match 选项（methods 选项除外），例如：
```.php
$this->matchResources('documents', ['extra' => $callback]);
```

## Scope 匹配
Scope 匹配用于限制和修改根路径，根路径只能是静态路径，例如：
```.php
$this->matchScope('admin', function() {
   if ($this->match('settings')) return;
});
```

等价与：
```.php
if ($this->match('admin/settings')) return;
```

## 获取 App 对象
```.php
$app = $this->getApp();
```
## 获取请求路径
```.php
$path = $this->getRequestPath();
```
## 设置/获取 Action
设置：
```.php
$this->setAction('preview');
```
获取：
```.php
$action = $this->getAction();
```
## 设置/获取 Action 方法
设置：
```.php
$this->setActionMethod('doPreviewAction');
```
获取：
```.php
$method = $this->getActionMethod();
```
## 设置/获取 Controller
设置：
```.php
$this->setController('documents');
```
获取：
```.php
$controller = $this->getController();
```
## 设置/获取 Controller 类
设置：
```.php
$this->setControllerClass('DocumentsController');
```
获取：
```.php
$class = $this->getControllerClass();
```
## 设置/获取 Module
设置：
```.php
$this->setModule('admin');
```
获取：
```.php
$module = $this->getModule();
```
## 路由参数
设置：
```.php
$this->setParam('query', 'hyperframework');
```
删除：
```.php
$this->removeParam('query');
```
获取单个路由参数：
```.php
$query = $this->getParam('query');
```
获取全部路由参数：
```.php
$params = $this->getParams();
```
查询路由参数是否存在：
```.php
$hasQuery = $this->hasParam('query');
```
## 重定向
```.php
$this->redirect('/path');
```
默认使用 302 重定向，可以重写，例如：
```.php
$this->redirect('/path', 301);
```
重定向方法会调用应用的 quit 方法退出应用。
## 设置/获取匹配状态
设置：
```.php
$this->setMatchStatus(true);
```
获取：
```.php
$isMatched = $this->isMatched();
```
