<?php
return array(
  'ClassLoader' => array(
    'app',
    'lib',
    HYPERFRAMEWORK_PATH.'web/lib', '../common/lib',
    '../../common/view',
    '../../../db',
  ),
  'Application' => array('Action', 'View' => 'Screen'),
);