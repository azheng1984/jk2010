<?php
$this->extend('_layouts/html');
$this->setBlock('body', function() {?>
    header
    <div id="content">
        <?php $this->renderBlock('menu'); ?>
        <?php $this->renderBlock('left'); ?>
        <?php $this->renderBlock('footer'); ?>
    </div>
    footer
<?php });
