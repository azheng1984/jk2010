<?php
return array(
    'name' => 'hf',
    'namespace' => 'Tc',
    'version' => '1.1.1',
    'description' => 'Hyperframework CLI Tool',
    'usage' => array(
        'usage_name' => '--main --opt[=<arg>] (--opt1|--opt2|--opt3) <arg>',
        '--main --opt[=<arg>] (--opt1opt2|--opt3)<arg>',
        '[options] [<arg>]',
        '[options] command'
    ),
    'repeatable_options' => array('-c', '--man', '--list'),
    'mutex_options' => array('xx'),
    'options' => array(
        array(
            'group name',
            array(
                '--name' => ''
            )
        ),
        '-x,--opt[=(ax-d|bd-sf|cds-fadf)]',
        '--name-of-option',
        '--name-of-option2' => 'description',
        '-n,--name-of-option3[=<arg-name>]' => array(
            'description' => 'x',
            'repeatable',
            'required'
        ),
        '--name-of-option[=<arg-key>]' => 'message',
    ),
//  'class' => 'CommandCollection', //default null
    'commands' => array( //subcommands = 'folder'
        array(
        ),
        'hello' => array( //inline. lazy load is supported
//          'alias' => 'hill',
            'usage' => '',
            'description' => 'Build application',
//          'class' => 'HelloCommand', //default
            'options' => array(
                '-h, --hi' => array('multiple'),
                '--hi2' => array('description' => 'hello hi2'),
                '--flatoption',
            ),
        ),
    )
);
