<?php
$this->setLayout('html');
$this->setLayout('/_layouts/html.php');

$this->render('_topic_views');
$this->render('/article/_topic_views.php');
$this->render('/_common/adsense.php');

$this->render('/_layouts/html.php');
