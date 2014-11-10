<?php
$this->setLayout('html.php');
$this->setBlock('body', function() {?>
    header
    <div id="content">
        <?php $this->renderBlock('menu'); ?>
        <?php $this->renderBlock('left'); ?>
        <?php $this->renderBlock('footer'); ?>
    </div>
    footer
<?php });
