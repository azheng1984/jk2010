<?php
return array(
  'sub' => array(
    'init' => 'AmazonCnInitCommand',
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