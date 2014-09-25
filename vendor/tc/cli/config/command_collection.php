<?php
return array(
    'name' => 'hf',
    'namespace' => 'Tc',
    'version' => '1.1.1',
    'description' => 'Hyperframework CLI Tool',
    'arguments' => array('arg1', '[arg2]...'),
    'options' => array(
        array(
            'title' => 'f1 group',
            '--f1' => array('repeatable'),
            array(
                '--good' => '',
                '--bad' => '',
                'mutex',
                //'required',
                //'repeatable'
            )
        ),
        array(
            'title' => 'f2 group',
            '--f2' => array('repeatable'),
            array(
                '--slow' => '',
                '--fast' => '',
                'mutex',
            )
        ),
        '-x,--opt[=(ax-d|bd-sf|cds-fadf)]',
        '--name-of-option',
        '--name-of-option2' => 'description',
        '-n,--name-of-option3[=<arg-name>]' => array(
            'description' => 'x',
            'repeatable',
            'required',
        ),
        '--name-of-option[=<arg-key>]' => 'message',
        'mutex' => array(
            '--f1', '--f2', 'required'
        ),
    ),
//  'class' => 'CommandCollection', //default null
    'commands' => array( //subcommands = 'folder'
        array(
        ),
        'hello' => array( //inline. lazy load is supported
//          'alias' => 'hill',
            'description' => 'Build application',
//          'class' => 'HelloCommand', //default
            'options' => array(
                '-h,--hi=<arg>' => array('repeatable'),
                '--hi2' => array('description' => 'hello hi2'),
                '--flatoption',
            ),
        ),
    )
);
