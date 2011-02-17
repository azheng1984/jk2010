<?php
class DocumentMetaScreen {
  public function render($item) {
    echo '<div class="meta">';
    $outputs = array();
    foreach (array('time', 'place', 'people') as $meta) {
      if (isset($item[$meta])) {
        $outputs[] = '<span class="'.$meta.'">'.$item[$meta].'</span>';
      }
    }
    $outputs[] = '<span class="source">'
                .$_ENV['source'][$item['source_id']][0].'</span>';
    echo implode(' | ', $outputs);
    echo '</div>';
  }
}