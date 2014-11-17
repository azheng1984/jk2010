<?php
$this->usePath('_system/admin/user');
$this->extend('/_layouts/app');

$this->load('user/load');
$this->load('_topic_views');
$this->load('user/_topic_views');
$this->load('user/_common/adsense');

$this->load('/_layouts/html');
