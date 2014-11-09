<?php
$this->setLayout('html.php');
$this->setBlock('body', function() {?>
header
<div id="content">
    <?php $this->renderBlock('content'); ?>
</div>
footer
<?php });
