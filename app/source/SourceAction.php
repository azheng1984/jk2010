<?php
class SourceAction {
  private $id;
  private $source;
  private $databaseIndex;
  private $categoryUrlName;

  public function GET() {
    if (!isset($_ENV['category'][$_GET['category']])) {
      throw new NotFoundException;
    }
    if (!is_numeric($_GET['database_index'])
     || !isset($_ENV['document_database'][(int)$_GET['database_index']])) {
      throw new NotFoundException;
    }
    if (!is_numeric($_GET['id'])) {
      throw new NotFoundException;
    }
    $this->id = $_GET['id'];
    $this->databaseIndex = $_GET['database_index'];
    $this->categoryUrlName = $_GET['category'];
    $db = new DocumentDb($this->databaseIndex);
    $connection = $db->getConnection();
    $statement = $connection->prepare("SELECT source_url FROM {$this->categoryUrlName}_document WHERE id=?");
    $statement->execute(array($this->id));
    $this->source = $statement->fetchColumn();
    if ($this->source === false) {
      throw new NotFoundException;
    }
    header('Location: http://'.$this->source);
  }
}