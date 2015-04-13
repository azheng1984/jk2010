# 视图
视图文件默认存放在

## 视图对象
在视图中，$this 等于视图对象。视图不能访问视图对象的 protected 或 private 成员。

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

## 视图工厂


## 视图路径构建器

## 视图对象成员智能提示
可以通过 phpdoc 标注视图中 $this 的类型，例如：
```php
/* @var $this Hyperframework\Web\View */
```
由于视图类中的 protected 和 private 成员都会被列出，如果要让提示更加精确，被标注的视图类不能包含任何 protected 或 private 成员。因此，在视图类中不应该使用 protected 成员，如果需要使用 private 成员，应当使用 “ViewKernel + View” 模式把具体实现封装在 ViewKernel 中。
