<?php
return array(
  'application' => array(
    'class' => 'ApplicationCacheBuilder',
    'sub' => array(
      'action' => 'ActionProcessorCacheBuilder',
      'view' => 'ViewProcessorCacheBuilder',
    ),
  ),
  'class_loader' => 'ClassLoader',
);