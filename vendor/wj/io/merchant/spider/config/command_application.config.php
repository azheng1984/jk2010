<?php
return array(
  'sub' => array(
    'init' => 'InitCommand',
    'run' => 'RunCommand',
    'retry' => 'RetryCommand',
    'show' => array(
      'class' => 'ShowCommand',
       'option' => array(
         'export_to_file' => array('short' => 'f'),
       )
    )
  )
);