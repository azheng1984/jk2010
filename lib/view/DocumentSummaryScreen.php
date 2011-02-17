<?php
class DocumentSummaryScreen {
  private $isRenderAnchorName;

  public function render($item, $isRenderAnchorName = false) {
    $this->isRenderAnchorName = $isRenderAnchorName;
    $hasImage = isset($item['image_url']);
    echo '<div class="item">';
    $this->renderText($item, $hasImage);
    if ($hasImage) {
      $title = "图：".$item['title'];
      echo '<div class="image"><img title="'.$title.'" alt="'.$title.'" src="'.$item['image_url'].'" /></div>';
    }
    echo '</div>';
  }

  private function renderTitle($item) {
    echo '<div class="title"><a target="_blank" ';
    if ($this->isRenderAnchorName) {
      echo 'name="', $item['url_name'], '" ';
    }
    echo 'href="', $item['url'], '">',
         $item['title'], "</a></div>";
  }

  private function renderDescription($item) {
    echo '<div class="description">'.$item['description'].'</div>';
  }

  private function renderText($item, $hasImage) {
    echo '<div class="text';
    if ($hasImage) {
      echo ' short_text';
    }
    echo '">';
    $this->renderTitle($item);
    $this->renderDescription($item);
    $meta = new DocumentMetaScreen;
    $meta->render($item);
    echo '</div>';
  }
}