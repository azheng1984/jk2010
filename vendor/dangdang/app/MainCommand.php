<?php
class MainCommand {
  private $categoryListLinks = array(
    'MobileCategoryList' => array(
      'm.dangdang.com' => array(
        '图书' => '/category_list.php?cid=01',
        '音乐' => '/category_list.php?cid=03',
        '影视' => '/category_list.php?cid=05',
      ),
    ),
    'CategoryList' => array(
      'category.dangdang.com' => array(
        '美妆' => '/list?cat=4002074',
        '手机数码' => '/list?cat=4003613',
        '母婴' => '/list?cat=4001940',
        '家居日用' => '/list?cat=4003900',
        '服装' => '/list?cat=4003844',
        '手机数码' => '/list?cat=4003613',
        '电脑办公' => '/list?cat=4003819',
        '家电' => '/list?cat=4001001',
        '食品' => '/list?cat=4002145',
        '鞋' => '/list?cat=4003872',
        '箱包皮具' => '/list?cat=4001829',
        '手表饰品' => '/list?cat=4003639',
        '运动户外' => '/list?cat=4003728',
        '汽车用品' => '/list?cat=4002429',
        '家具装饰' => '/list?cat=4003760',
        '玩具' => '/list?cat=4002061',
      ),
    )
  );

  public function execute() {
    if (Lock::execute() === false) {
      echo 'locked'.PHP_EOL;
      return;
    }
    DbTask::initialize();
    if (DbTask::isEmpty() === true) {
      $this->initialize();
    }
    while (DbTask::moveToNext() !== false) {
      $task = DbTask::get();
      $result = $this->dispatch($task);
      if ($result !== null) {
        DbTask::fail($task['id'], $result);
      }
      $this->show($result);
      DbTask::remove();
    }
  }

  private function initialize() {
    foreach ($this->categoryListLinks as $type => $item) {
      foreach ($item as $domain => $pathes) {
        foreach ($pathes as $name => $path) {
          DbTask::add(
            $type, array(
              'name' => $name,
              'path' => $path,
              'domain' => $domain,
              'parent_category_id' => null
            )
          );
        }
      }
    }
  }

  private function dispatch($task) {
    $class = $task['type'].'Processor';
    $parser = new $class;
    return $parser->execute(eval('return '.$task['arguments'].';'));
  }

  private function show($result) {
    if ($result === null) {
      echo '.';
      return;
    }
    echo 'x';
  }
}