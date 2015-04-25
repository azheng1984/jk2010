# Active Record 基础
## 创建活动记录
```.php
use Hyperframework\Db\ActiveRecord;

class Article extends ActiveRecord {
    public function getId() {
        return $this->getColumn('id');
    }

    public function getTitle() {
        return $this->getColumn('title');
    }

    public function setTitle($value) {
        $this->setColumn('title', $value);
    }

    public function getContent() {
        return $this->getColumn('content');
    }

    public function setContent($value) {
        $this->setColumn('content', $value);
    }
}
```
默认类名和表名一致，可以通过重写 getTableName 静态方法返回定制的表名。

每个 active record 对应的表必须包含 id 列，并且必须是自增型。

## 插入活动记录
```.php
$article = new Article;
$article->setTitle('title');
$article->setContent('content');
$article->insert();
```
如果插入成功，活动记录的 id 列会被赋值。

## 查询活动记录
查询单个活动记录：
```.php
$where = 'title = ?';
$params = ['title'];
$article = Article::find($where, $params);
```
$params 参数可以省略。如果活动记录不存在，则返回 null。

根据 sql 查询单个活动记录：
```.php
$sql = 'SELECT * FROM Article WHERE title = ?';
$params = ['title'];
$article = Article::findBySql($sql, $params);
```
$params 参数可以省略。如果活动记录不存在，则返回 null。

根据 id 查询单个活动记录：
```.php
$article = Article::findById(1);
```
如果活动记录不存在，则返回 null。

查询多个活动记录：
```.php
$where = 'id > ?';
$params = [1];
$articles = Article::find($where, $params);
```

根据 sql 查询多个活动记录：
```.php
$sql = 'SELECT * FROM Article WHERE id = ?';
$params = [1];
$articles = Article::findAllBySql($sql, $params);
```
$params 参数可以省略。
## 更新活动记录
```.php
$article = Article::findById(1);
$article->setTitle('new title');
$article->update();
```

## 删除活动记录
```.php
$article = Article::findById(1);
$article->delete();
```

## 统计查询
### count
```.php
$where = 'id < ?';
$params = [100];
$count = Article::count($where, $params);
```
参数 $where 和 $params 可选，返回计数值。
### min
```.php
$where = 'id < ?';
$columnName = 'view_count';
$params = [100];
$min = Article::min($columnName, $where, $params);
```
参数 $where 和 $params 可选，返回最小值。
### max
```.php
$where = 'id < ?';
$columnName = 'view_count';
$params = [100];
$max = Article::max( $columnName, $where, $params);
```
参数 $where 和 $params 可选，返回最大值。
### sum
```.php
$where = 'id < ?';
$columnName = 'view_count';
$params = [100];
$sum = Article::sum($columnName, $where, $params);
```
参数 $where 和 $params 可选，返回总和。
### average
```.php
$where = 'id < ?';
$params = [100];
$average = Article::average($columnName, $where, $params);
```
参数 $where 和 $params 可选，返回均值。
## 获取列
```.php
$value = $this->getColumn($name);
```
getColumn 是 protected 方法。如果列不存在，则返回 null。
## 设置列
```.php
$this->setColumn($name, $value);
```
setColumn 是 protected 方法。

## 获取行
```.php
$row = $this->getRow();
```
getRow 是 protected 方法。

## 设置行
```.php
$this->setRow($row);
```
setRow 是 protected 方法。

## 查询列是否存在
```.php
$this->hasColumn($name);
```
hasColumn 是 protected 方法。
## 删除列
```.php
$this->removeColumn($name);
```
removeColumn 是 protected 方法。
## 获取表名
```.php
$table = Article::getTableName();
```
## 加载活动记录
```.php
$record = static::load($row);
```
load 是 protected 静态方法。
