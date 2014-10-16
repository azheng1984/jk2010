<?php
return array(
    'name' => 'hf',
    'namespace' => 'Tc', //default: app_root_namespace
    'version' => '1.1.1',
    'description' => 'Hyperframework CLI Tool', //optional
    'arguments' => array('<arg1>', '<arg2>...'),
    //'pass_options' => false, //commands only, hf.cli.commands.pass_options to set all
    // = false to disable passing collection & commands options
    //default: use command execute function signature
    //collection must not have arguments
    'options' => array(
        array(
            '--good' => '', '--bad' => '', 'mutex', 'required', 'repeatable'
        ),
        array(
            '--slow' => '',
            '--fast' => '',
            'mutex',
        ),
        '-x <arg>',
        '-x (a|b|c)',
        '-x[(a|b|c)]',
        '-x[<arg>]',
        '-x, --opt[=(ax-d|bd-sf|cds-fadf)]',
        '--name-of-option',
        '--name-of-option2' => 'description',
        '-n, --name-of-option3 <arg-name>',
        '-n, --name-of-option3=<arg-name>',
        '-n, --name-of-option3[=<arg-name>]' => array(
            'description' => 'x',
            'repeatable',
            'required',
        ),
        '--name-of-option[=<arg-key>]' => 'message',
    ),
    //'class' => 'CommandCollection', //default CommandCollection
    //\Xx\CommandCollection //same as php
    //CommandCollection if have options
    'commands' => array( //or subcommands = 'folder' default to config/commands
        'hello' => array( //inline. lazy load is supported
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
