<?php
class EditCollectionAction {
  public function GET() {
    var_dump(unpack('L', pack('L', 72837283728)));
    echo 9223372036854775805 + 1;
    //18446744073709551615
    
  }

  public function POST() {
    $parser = new Markdown;
    echo $parser->transform($_POST['content']);
    var_dump($_POST['content']);
    $lineList = explode("\n",trim($_POST['content']));
    $lineIdTextList = '';
    foreach ($lineList as $line) {
      $line = trim($line);
      if ($line === '') {
        $lineIdTextList .= "\n";
        continue;
      }
      $sha1 = sha1($line, true);
      $id = Db::getColumn(
        'SELECT id FROM line WHERE book_id = 1 AND sha1 = ?', $sha1
      );
      if ($id === false) {
        Db::insert('line', array(
          'book_id' => 1, 'content' => $line, 'sha1' => $sha1
        ));
        $id = Db::getLastInsertId();
      }
      $lineIdTextList .= $id."\n";
    }
    $pageId = $GLOBALS['PATH_SECTION_LIST'][3];
    $lineIdTextList = trim($lineIdTextList);
    $sha1 = sha1($lineIdTextList, true);
    //TODO sha1 检查是否 page 存在
    Db::insert('page', array(
      'book_id' => 1, 'name_line_id' => 1,
      'line_id_list' => $lineIdTextList, 'sha1' => $sha1,
      'creation_time' => date('Y-m-d H:i:s')
    ));
    $pageId = Db::getLastInsertId();
    //TODO 把当前 book 插入 history 页面, 更新 book page list(str_replace)
    //301重定向到新页
  }
}