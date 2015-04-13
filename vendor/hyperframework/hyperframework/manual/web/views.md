# 视图
## 视图文件
视图文件默认存放在项目根目录的 views 文件夹下。可以通过配置修改：
```.php
Config::set('hyperframework.web.view.root_path', 'custom_view_root');
```

视图文件默认使用 .html.php 后缀，php 表示视图文件格式，html 表示输出格式。

通过配置隐藏输出格式：
```.php
Config::set('hyperframework.web.view.path.include_output_format', false);
```
默认值：true

通过配置修改默认输出格式：
```.php
Config::set('hyperframework.web.view.default_output_format', 'xml');
```
默认值：html

通过配置修改是视图文件格式：
```.php
Config::set('hyperframework.web.view.format', 'tpl');
```
默认值：php

## 视图对象
在视图中，$this 等于视图对象。视图不能访问视图对象的 protected 或 private 成员。

可以通过配置修改视图对象类型：
```.php
Config::set('hyperframework.web.view.class', 'CustomView');
```
默认值：Hyperframework\Web\View

## 视图模型
视图模型是一个数组，可以通过视图对象的数组访问形式获取字段对应的值，例如：
```.php
echo $this['title'];
```
Controller 默认会把 action 返回值作为视图模型。

## 区块
通过视图对象设置区块，例如：
```.php
echo $this->setBlock('block', function() {
    echo 'content';
});
```

通过视图对象渲染区块，例如：
```.php
echo $this->renderBlock('block');
```

为区块设定默认内容，例如：
```.php
echo $->renderBlock('block', function() {
    echo 'default content';
});
```

## 布局
设置布局：
```.php
echo $this->setLayout('main');
```

## 视图对象成员智能提示
可以通过 phpdoc 标注视图中 $this 的类型，例如：
```php
/* @var $this Hyperframework\Web\View */
```
由于被标注类中的 protected 和 private 成员都会被列出，如果要让提示更加精确，被标注的视图类不能包含任何 protected 或 private 成员。因此，在视图类中不应该使用 protected 成员。如果需要使用 private 成员，应当遵循 “ViewKernel + View” 模式，把具体实现封装在 ViewKernel 中。
