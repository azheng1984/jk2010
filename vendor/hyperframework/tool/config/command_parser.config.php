<?php
return array(
  'description' => 'Hyperframework CLI Tool - Version 0.1',
  'sub' => array(
    'build' => array(
      'description' => 'Build application',
      'class' => 'BuildCommand',
    ),
    'new' => array(
      'description' => 'Create scaffold',
      'class' => 'NewCommand',
    ),
  ),
);