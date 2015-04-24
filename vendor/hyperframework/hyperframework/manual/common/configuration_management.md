# 配置管理
Config 类位于 Hyperframework\Common 命名空间下。
## 获取配置项
```.php
$value = Config::get($name);
```
参数 $name 是一个字符串。

可以为配置添加默认值，例如：

```.php
$value = Config::get($name, 'default');
```

当 $name 不存在时，返回默认值。

### 获取字符串配置项
```.php
$value = Config::getString($name);
```
可以为配置添加默认值，例如：
```.php
$value = Config::getString($name, 'default');
```

### 获取布尔型配置项
```.php
$value = Config::getBool($name);
```
可以为配置添加默认值，例如：
```.php
$value = Config::getBool($name, true);
```
### 获取整型配置项
```.php
$value = Config::getInt($name);
```
可以为配置添加默认值，例如：
```.php
$value = Config::getInt($name, 1);
```
### 获取浮点型配置项
```.php
$value = Config::getFloat($name);
```
可以为配置添加默认值，例如：
```.php
$value = Config::getFloat($name, 1.0);
```

### 获取应用根路径
```.php
$value = Config::getAppRootPath();
```

### 获取应用根命名空间
```.php
$value = Config::getAppRootNamespace();
```

### 获取所有配置项
```.php
$configs = Config::getAll();
```

### 设置单个配置项
```.php
Config::set($name, $value);
```

### 导入配置数组
```.php
Config::import($data);
```
参数 $data 是一个包含配置名称和值的二维数组，也可包含配置段，例如：
```.php
$data = [
    'my.config_1' => 'a',
    'my.config_2' => 'b'
];
```
等价与：
```.php
$data = [
    '[my]',
    'config_1' => 'a',
    'config_2' => 'b'
];
```

### 导入配置文件
```.php
Config::importFile($path);
```
默认配置根目录是项目根目录中的 config 文件夹。

## 查询配置项是否存在
```.php
$status = Config::has($name);
```

## 移除配置项
```.php
Config::remove($name);
```
