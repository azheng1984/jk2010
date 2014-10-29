<?php
$this->setLayout();
return function() {?>
<div>hello world</div>
  <div><?php echo $this->article['title'] ?></div>
<?php };
echo '<xml>';
