<?php
class MainCommand {
  private $categoryListLinks = array(
//    'MobileCategoryList' => array(
//      'm.dangdang.com' => array(
//        '图书' => '/category_list.php?cid=01'
//      ),
//    ),
    'CategoryList' => array(
      'category.dangdang.com' => array(
        '时尚美妆' => '/list?cat=4002074'
      ),
    )
  );

  public function execute() {
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