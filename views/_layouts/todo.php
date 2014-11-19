<?php
$this->setLayout('_layouts/html');
$this->setBlock('body', function() {?>
    header
    <div id="content">
        <?php $this->renderBlock('menu'); ?>
        <?php $this->renderBlock('left'); ?>
        <?php $this->renderBlock('content'); ?>
        <?php $this->renderBlock('footer'); ?>
    </div>
    footer
<?php });
