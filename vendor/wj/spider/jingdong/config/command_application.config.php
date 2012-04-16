<?php
return array(
  'sub' => array(
    'init' => 'JingDongInitCommand',
    'run' => 'RunCommand',
    'retry' => 'RetryCommand',
    'import' => 'JingDongImporterCommand',
    'show' => array(
      'class' => 'ShowCommand',
      'option' => array(
        'export_to_file' => array('short' => 'f'),
      )
    )
  )
);