<?php
return array(
  'ClassLoader' => array(
    'app',
    'lib',
    HYPERFRAMEWORK_PATH.'web/lib', '../common',
    '../../common/view',
  ),
  'Application' => array('Action', 'View' => 'Screen'),
);