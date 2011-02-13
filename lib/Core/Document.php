<?php
class Document {
  private $id;
  private $title;
  private $time;
  private $place;
  private $people;
  private $up;
  private $down;
  private $source;
  private $url;

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
    $sql = "SELECT * FROM DAta";
  }
}