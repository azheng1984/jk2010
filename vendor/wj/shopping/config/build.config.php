<?php
return array(
  'ClassLoader' => array('app', 'lib', HYPERFRAMEWORK_PATH.'web/lib', DB_PATH),
  'Application' => array('Action', 'View' => array('Screen', 'Json')),
  'Asset' => '+'
);