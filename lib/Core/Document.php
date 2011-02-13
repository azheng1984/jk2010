<?php
class Document {
  private $id;
  private $title;
  private $default_image_id;
  private $default_sentence_id;
  private $unique_name;
  private $time;
  private $place;
  private $people;
  private $up;
  private $down;
  private $source_name;
  private $source_url;
  private $related_elements; //可以分布在不同的数据库，通过缓存解决性能问题
  //可以通过本地服务器批处理生成缓存，然后上传到服务器上，在本地过期，服务器端不需要维持 sentence 到 document 的关系，
 //但是本地最好维护，因为可以在修改/删除数据时更新缓存
  private $category_page_number;

  public function __construct($id, $title, $time, $place,
                              $people, $up, $down, $source, $url) {
   $this->id = $id;
   $this->title = $title;
   $this->time = $time;
   $this->place = $place;
   $this->people = $people;
   $this->up = $up;
   $this->down = $down;
   $this->source = $source;
   $this->url = $url;
  }

  public function getItem($id) {
    $sql = "SELECT * FROM Data";
  }
}