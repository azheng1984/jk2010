# 视图
视图文件默认存放在

## 视图上下文
在视图中，$this 等于视图对象，视图对象在不同的视图中是相同的。

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
echo $this->renderBlock('block', function() {
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


