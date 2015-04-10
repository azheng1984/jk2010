# 路由
## 简介
通过实现 `Hyperframework\Web\Router` 类的 `execute` 抽象方法来处理路由逻辑。`execute` 方法在 `Router` 的构造函数中被调用。

`execute` 方法可以返回一个 `bool` 值，表示路由是否已经匹配。或者返回一个字符串，表示已经匹配，同时设置 `module`、`controller` 和 `action`，格式如下：

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
也可以超过三个层，除了末尾两层，其他都是 module，例如：
```.php
return 'module_segment_1/module_segment_2/controller/action';
```

execute 执行完成后，如果匹配状态等于 false，将会抛出 NotFoundException。

## 规则匹配
### 匹配静态路径
```.php
$this->match('segment');
```

所有匹配规则都必须使用相对路径（默认基于根路径，可以通过 matchScope 修改）。如需匹配顶层路径，则使用 '/' 规则，例如：

```.php
$this->match('/');
```

### 匹配动态路径片段
```.php
$this->match(':segment');
```

如果匹配，可以通过 getParam 获取对应的值，例如：

```.php
$this->getParam('segment');
```

### 可选片段
```.php
$this->match('required(/optional)');
```

### 匹配 module、controller 和 action
:module、:controller 和 :action 动态段会被用来设置 module、controller 和 action。

```.php
$this->match(':module/:controller/:action');
```

### 使用路径片段通配符
```.php
$this->match('*wildcard');
```

和动态路径片段不同的是，通配符匹配是贪婪匹配，匹配会跨越 "/"。

如果匹配成功，可以通过 getParam 获取对应的值。

### 匹配选项
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

#### 动态片段格式
可以使用正则定义动态规则的格式，例如：
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

## 单个资源匹配
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

### Action 定义
```.php
$actions = ['preview'];
```
等价与：
```.php
$actions = ['preview' => [['GET'], 'preview']];
```

第一个元素是字符串（定义一个 Http 请求方法限制）或数组（定义多个 Http 请求方法限制），默认值是 'GET'。

第二个参数是请求的相对路径，默认和 action 名称相同。相对路径基于资源路径，例如，资源路径是 sitemap，action 路径是 preview，那么访问此 action 的路径是 sitemap/preview。

可以加入更多键值对来限定 action 匹配规则，用法和 match 方法的选项相同。例如：
```.php
$actions = ['preview' => ['extra' => $callback]];
```

### 资源匹配选项
#### actions

#### extra_actions
```.html
$this->matchResource('article', ['extra_actions' => ['preview']]);
```

#### ignored_actions

#### 更多选项
更多资源选项和 match 方法的选项相同。

## 资源集合匹配
```.php
$this->matchResource('articles');
```

此时 controller 等于 articles， action 对应关系：

| HTTP 方法 | 路径               | action                 |
| --------- | ------------------ | ------ |
| GET       | /articles          | index  |
| GET       | /articles/:id      | show   |
| GET       | /articles/new      | new    |
| GET       | /articles/:id/edit | edit   |
| POST      | /articles          | create |
| PUT/PATCH | /articles/:id      | update |
| DELETE    | /articles/:id      | delete |

#### actions

#### collection_actions

#### element_actions

#### extra_collection_actions

#### extra_element_actions

#### ignored_actions

#### id
默认值 \d+

#### 更多选项
更多资源集合选项和 match 方法的选项相同。

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
