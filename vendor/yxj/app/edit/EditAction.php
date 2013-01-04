<?php
class EditAction {
  public function GET() {
    echo 'xx';
  }

  public function POST() {
    $parser = new Markdown;
    echo $parser->transform($_POST['content']);
    $lineList = explode("\n", $_POST['content']);
    foreach ($lineList as $line) {
      if ($line === '') {
        continue;
      }
      var_dump($line);
      echo 'SELECT id FROM line WHERE page_id = 1 AND content = "'.$line.'"';
      $id = Db::getColumn(
        'SELECT id FROM line WHERE page_id = 1 AND content = "'.$line.'"'
      );
      var_dump($id);
    }
  }
}