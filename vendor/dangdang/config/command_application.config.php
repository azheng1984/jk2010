<?php
return array(
  'sub' => array(
    'init' => 'DangdangInitCommand',
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